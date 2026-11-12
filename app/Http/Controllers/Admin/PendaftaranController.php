<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationStatusHistory;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PendaftaranController extends Controller
{
    /**
     * Tampilkan daftar calon siswa yang sedang mendaftar (data dari DB).
     */
    public function index(Request $request): View
    {
        $query = Application::with(['batch'])
            ->orderByDesc('created_at');

        // Pencarian nama, wali, nomor registrasi, atau sekolah.
        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($q) use ($kata) {
                $q->where('full_name', 'like', "%{$kata}%")
                    ->orWhere('guardian_name', 'like', "%{$kata}%")
                    ->orWhere('registration_number', 'like', "%{$kata}%")
                    ->orWhere('school_name', 'like', "%{$kata}%");
            });
        }

        // Filter tab status. "Semua" hanya memuat pendaftaran yang masih
        // menunggu aksi admin, status yang sudah final (diterima/ditolak/batal)
        // baru tampil lewat tabnya masing-masing.
        $tabStatuses = [
            'semua' => ['menunggu_review', 'perlu_perbaikan'],
            'review' => ['menunggu_review'],
            'perbaikan' => ['perlu_perbaikan'],
            'diterima' => ['diterima'],
            'ditolak' => ['ditolak', 'batal'],
        ];

        $activeTab = $request->input('status');
        if (! isset($tabStatuses[$activeTab])) {
            $activeTab = 'semua';
        }

        $query->whereIn('status', $tabStatuses[$activeTab]);

        $pendaftar = $query->paginate(10)->withQueryString();

        // Angka pokok admisi (KPI) dari DB.
        $kpis = [
            [
                'label' => 'Menunggu Verifikasi',
                'nilai' => Application::where('status', 'menunggu_review')->count(),
                'satuan' => 'Calon Siswa',
                'ikon' => 'clipboard-form',
                'aksen' => 'border-t-gold',
                'tile' => 'bg-sandgold text-bronze',
                'bawah' => 'sla',
            ],
            [
                'label' => 'Perlu Perbaikan',
                'nilai' => Application::where('status', 'perlu_perbaikan')->count(),
                'satuan' => 'Berkas',
                'ikon' => 'refresh',
                'aksen' => 'border-t-gold',
                'tile' => 'bg-sand text-ink',
                'bawah' => 'chip',
            ],
            [
                'label' => 'Diterima Bulan Ini',
                'nilai' => Application::where('status', 'diterima')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'satuan' => 'Siswa Baru',
                'ikon' => 'users-check',
                'aksen' => 'border-t-espresso',
                'tile' => 'bg-espresso text-gold',
                'bawah' => 'konversi',
            ],
            [
                'label' => 'Ditolak / Batal',
                'nilai' => Application::whereIn('status', ['ditolak', 'batal'])->count(),
                'satuan' => 'Calon Siswa',
                'ikon' => 'slash-circle',
                'aksen' => 'border-t-line',
                'tile' => 'bg-sand text-ink',
                'bawah' => 'alasan',
            ],
        ];

        $tabs = [
            ['key' => 'semua', 'label' => 'Semua', 'jumlah' => Application::pendingReview()->count()],
            ['key' => 'review', 'label' => 'Menunggu Review', 'jumlah' => Application::where('status', 'menunggu_review')->count()],
            ['key' => 'perbaikan', 'label' => 'Perlu Perbaikan', 'jumlah' => Application::where('status', 'perlu_perbaikan')->count()],
            ['key' => 'diterima', 'label' => 'Diterima', 'jumlah' => Application::where('status', 'diterima')->count()],
            ['key' => 'ditolak', 'label' => 'Ditolak', 'jumlah' => Application::whereIn('status', ['ditolak', 'batal'])->count()],
        ];

        return view('admin.pendaftaran', compact('pendaftar', 'kpis', 'tabs', 'activeTab'));
    }

    /**
     * Tampilkan detail satu calon siswa berdasarkan nomor pendaftaran (data dari DB).
     */
    public function show(string $kode): View
    {
        $pendaftar = Application::where('registration_number', $kode)
            ->with(['batch', 'studyShift', 'documents.file', 'student'])
            ->firstOrFail();

        return view('admin.pendaftaran-detail', compact('pendaftar'));
    }

    /**
     * Tayangkan berkas dokumen pendaftaran secara inline untuk verifikasi admin.
     */
    public function showDocument(string $kode, ApplicationDocument $dokumen): Response
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        // Pastikan dokumen milik aplikasi ini.
        if ($dokumen->application_id !== $application->id) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $file = $dokumen->file;
        if (! $file) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $path = $file->path;
        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Berkas tidak tersedia di storage.');
        }

        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
        ]);
    }

    /**
     * Update status aplikasi (diterima / ditolak / perlu_perbaikan) oleh admin.
     */
    public function updateStatus(Request $request, string $kode): RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:diterima,ditolak,perlu_perbaikan'],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        // review_note wajib untuk ditolak dan perlu_perbaikan
        if (in_array($validated['status'], ['ditolak', 'perlu_perbaikan'], true)
            && empty($validated['review_note'])) {
            return back()->withErrors([
                'review_note' => 'Catatan review wajib diisi untuk status ini.',
            ])->withInput();
        }

        // Validasi transisi: hanya dari menunggu_review atau perlu_perbaikan
        $allowedFrom = ['menunggu_review', 'perlu_perbaikan'];
        if (! in_array($application->status, $allowedFrom, true)) {
            return back()->withErrors([
                'status' => 'Status tidak dapat diubah dari '.$application->status.'.',
            ])->withInput();
        }

        $oldStatus = $application->status;

        DB::transaction(function () use ($application, $validated, $oldStatus) {
            $application->update([
                'status' => $validated['status'],
                'status_reason' => $validated['review_note'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'reason' => $validated['review_note'] ?? null,
                'changed_by' => auth()->id(),
            ]);

            // Saat diterima, buat data siswa induk dari pendaftaran supaya
            // muncul di halaman Data Siswa & Kelas (data asli dari DB, bukan mock).
            if ($validated['status'] === 'diterima' && $application->student_id === null) {
                $this->createStudentFromApplication($application);
            }
        });

        $statusLabel = match ($validated['status']) {
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            default => $validated['status'],
        };

        return redirect()->route('admin.pendaftaran.detail', $kode)
            ->with('success', "Status pendaftaran berhasil diubah menjadi {$statusLabel}.");
    }

    /**
     * Admin verifikasi pembayaran (lunas / ditolak).
     */
    public function updatePembayaran(Request $request, string $kode): RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        $validated = $request->validate([
            'payment_status' => ['required', 'string', 'in:lunas,ditolak'],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($application->payment_status === 'lunas') {
            return back()->withErrors([
                'payment_status' => 'Pembayaran sudah lunas.',
            ])->withInput();
        }

        DB::transaction(function () use ($application, $validated) {
            $application->update([
                'payment_status' => $validated['payment_status'],
                'payment_verified_at' => now(),
                'payment_verified_by' => auth()->id(),
            ]);

            if ($validated['payment_status'] === 'lunas') {
                $application->update(['status_reason' => $validated['review_note'] ?? 'Pembayaran lunas.']);
            }
        });

        $label = $validated['payment_status'] === 'lunas' ? 'Lunas' : 'Ditolak';

        return redirect()->route('admin.pendaftaran.detail', $kode)
            ->with('success', "Status pembayaran berhasil diubah menjadi {$label}.");
    }

    /**
     * Generate invoice link Xendit untuk pendaftaran yang sudah diterima,
     * agar staff bisa kirim link pembayaran ke wali via WhatsApp secara manual.
     */
    public function generateXenditLink(string $kode): RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if ($application->status !== 'diterima') {
            return back()->withErrors([
                'generate_link' => 'Pendaftaran belum disetujui admin (status belum Diterima).',
            ]);
        }

        if ($application->payment_status === 'lunas') {
            return back()->withErrors([
                'generate_link' => 'Pembayaran sudah lunas.',
            ]);
        }

        // Ambil nominal biaya pendaftaran dari pengaturan, fallback default.
        $amount = (float) (WebsiteSetting::where('key', 'biaya_pendaftaran')->value('value') ?? 500000);

        $externalId = 'REG-'.$application->registration_number.'-'.Str::random(8);

        $response = Http::withBasicAuth(config('xendit.secret_key'), '')
            ->timeout(30)
            ->post(config('xendit.base_url').'/'.config('xendit.api_version').'/invoices', [
                'external_id' => $externalId,
                'amount' => $amount,
                'payer_email' => $application->email ?? '',
                'description' => 'Biaya Pendaftaran - '.$application->registration_number.' - '.$application->full_name,
                'invoice_duration' => 86400,
                'callback_url' => config('xendit.callback_url'),
                'redirect_url' => config('xendit.redirect_url'),
            ]);

        if ($response->failed()) {
            Log::error('Gagal generate link Xendit untuk pendaftaran '.$kode.': '.$response->body());

            return back()->withErrors([
                'generate_link' => 'Gagal membuat link pembayaran Xendit. Silakan coba lagi nanti.',
            ]);
        }

        $data = $response->json();

        $application->update([
            'xendit_invoice_id' => $data['id'] ?? null,
            'xendit_invoice_url' => $data['invoice_url'] ?? null,
        ]);

        return redirect()->route('admin.pendaftaran')
            ->with('success', 'Link pembayaran Xendit berhasil dibuat. Silakan salin dan kirim ke wali via WhatsApp.');
    }

    /**
     * Buat record Student + Guardian dari data pendaftaran yang diterima,
     * lalu link student_id ke application agar muncul di Data Siswa & Kelas.
     */
    private function createStudentFromApplication(Application $application): void
    {
        $student = Student::create([
            'nis' => 'QLF-'.now()->year.'-'.str_pad((string) $application->id, 4, '0', STR_PAD_LEFT),
            'nisn' => $application->nisn,
            'full_name' => $application->full_name,
            'nickname' => $application->nickname,
            'birth_place' => $application->birth_place,
            'birth_date' => $application->birth_date,
            'email' => $application->email,
            'phone_wa' => $application->phone_wa,
            'group_wa' => $application->group_wa,
            'study_shift_id' => $application->study_shift_id,
            'address' => $application->address,
            'school_name' => $application->school_name,
            'school_grade' => $application->school_grade,
            'education_level' => $application->education_level,
            'curriculum' => $application->curriculum,
            'major' => $application->major,
            'status' => 'aktif',
        ]);

        if ($application->guardian_name) {
            $guardian = Guardian::create([
                'name' => $application->guardian_name,
                'phone_wa' => $application->guardian_phone,
            ]);

            $student->guardians()->attach($guardian->id, [
                'relationship' => 'wali',
                'is_primary_contact' => true,
                'is_financial_responsible' => true,
            ]);
        }

        $application->update(['student_id' => $student->id]);
    }
}
