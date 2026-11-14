<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranBuatAkunRequest;
use App\Models\Application;
use App\Models\Student;
use App\Models\User;
use App\Services\XenditInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PendaftaranBuatAkunController extends Controller
{
    public function __construct(private readonly XenditInvoiceService $xenditInvoiceService) {}

    /**
     * Show the account creation form after payment lunas.
     *
     * Bila status pembayaran belum terkonfirmasi (webhook Xendit masih diproses),
     * halaman menunggu ditampilkan dan otomatis berubah jadi form begitu lunas.
     */
    public function show(string $kode): View|RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if ($application->status !== 'diterima') {
            abort(403, 'Pendaftaran belum disetujui admin.');
        }

        if ($application->accounts_created) {
            abort(403, 'Akun sudah telah dibuat untuk pendaftaran ini.');
        }

        // Callback Xendit belum tentu sampai (mis. aplikasi belum punya URL
        // publik), jadi status dicek langsung ke Xendit.
        $this->xenditInvoiceService->sinkronkanPendaftaran($application);

        if ($application->payment_status !== 'lunas') {
            return view('pendaftaran.menunggu-pembayaran', [
                'application' => $application,
            ]);
        }

        return view('pendaftaran.buat-akun', [
            'application' => $application,
        ]);
    }

    /**
     * Status pembayaran pendaftaran untuk polling halaman menunggu pembayaran.
     */
    public function status(string $kode): JsonResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        // Polling dari peramban ikut menyinkronkan status ke Xendit agar
        // status lunas terdeteksi walau callback belum diterima.
        $this->xenditInvoiceService->sinkronkanPendaftaran($application);

        return response()->json([
            'payment_status' => $application->payment_status,
            'lunas' => $application->payment_status === 'lunas',
            'accounts_created' => $application->accounts_created,
        ]);
    }

    /**
     * Create ortu and siswa user accounts.
     */
    public function store(PendaftaranBuatAkunRequest $request, string $kode): RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if ($application->status !== 'diterima') {
            abort(403, 'Pendaftaran belum disetujui admin.');
        }

        if ($application->payment_status !== 'lunas') {
            abort(403, 'Pembayaran belum lunas.');
        }

        if ($application->accounts_created) {
            abort(403, 'Akun sudah telah dibuat untuk pendaftaran ini.');
        }

        DB::transaction(function () use ($application, $request) {
            // Create ortu user
            $ortu = User::create([
                'name' => $request->ortu_name,
                'email' => $request->ortu_email,
                'phone' => $request->ortu_phone,
                'password' => Hash::make($request->ortu_password),
                'role' => 'ortu',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);

            // Create siswa user
            $siswa = User::create([
                'name' => $request->siswa_name,
                'email' => $request->siswa_email,
                'phone' => $request->siswa_phone,
                'password' => Hash::make($request->siswa_password),
                'role' => 'siswa',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);

            // Link siswa student record to application if exists
            if ($application->student_id) {
                $student = Student::find($application->student_id);

                if ($student) {
                    $student->update([
                        'user_id' => $siswa->id,
                        'email' => $request->siswa_email,
                    ]);

                    // Orang tua harus tertaut ke data wali agar dashboard ortu
                    // bisa menampilkan anaknya.
                    $guardian = $student->guardians()
                        ->wherePivot('is_primary_contact', true)
                        ->first() ?? $student->guardians()->first();

                    $guardian?->update([
                        'user_id' => $ortu->id,
                        'email' => $request->ortu_email,
                    ]);
                }
            }

            $application->update(['accounts_created' => true]);
        });

        return redirect()->route('pendaftaran.berhasil', $kode)
            ->with('success', 'Akun orang tua dan siswa berhasil dibuat. Silakan login.');
    }

    /**
     * Show success page.
     */
    public function berhasil(string $kode): View|RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if (! $application->accounts_created) {
            return redirect()->route('pendaftaran.buatAkun', $kode);
        }

        return view('pendaftaran.berhasil', [
            'application' => $application,
        ]);
    }
}
