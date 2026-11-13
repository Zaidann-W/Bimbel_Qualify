<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Room;
use App\Models\ScheduleRule;
use App\Models\StaffProfile;
use App\Models\StudyShift;
use App\Models\Subject;
use App\Services\JadwalSesiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManajemenJadwalController extends Controller
{
    /**
     * Rentang maksimum satu proses generate sesi, dalam hari.
     */
    private const MAKS_RENTANG_GENERATE = 92;

    /**
     * Tampilkan modul manajemen jadwal: aturan mingguan dan sesi terjadwal.
     */
    public function index(Request $request, JadwalSesiService $jadwal): View
    {
        $tab = $request->query('tab') === 'aturan' ? 'aturan' : ($request->query('tab') === 'ruang' ? 'ruang' : 'sesi');
        [$dari, $sampai] = $this->rentangDariRequest($request);
        $status = $this->statusDariRequest($request);

        $sesi = $jadwal->sesiDalamRentang($dari, $sampai, $status);

        return view('admin.manajemen-jadwal', [
            'tab' => $tab,
            'dari' => $dari,
            'sampai' => $sampai,
            'status' => $status,
            'sesi' => $sesi,
            'jumlahAturan' => ScheduleRule::count(),
            'aturan' => ScheduleRule::with(['classGroup', 'subject', 'staff', 'room', 'studyShift'])
                ->orderBy('weekday')
                ->orderBy('start_time')
                ->get(),
            'kelas' => ClassGroup::orderBy('code')->get(),
            'ruangan' => Room::withCount('classSessions')->orderBy('name')->get(),
        ]);
    }

    /**
     * Formulir untuk membuat aturan jadwal mingguan baru.
     */
    public function createAturan(): View
    {
        return view('admin.manajemen-jadwal-aturan-form', [
            'scheduleRule' => new ScheduleRule([
                'weekday' => 1,
                'start_time' => '15:30',
                'end_time' => '17:30',
            ]),
        ] + $this->referensiForm());
    }

    /**
     * Simpan aturan jadwal mingguan baru.
     */
    public function storeAturan(Request $request): RedirectResponse
    {
        $data = $this->validatedAturan($request);

        ScheduleRule::create($data);

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'aturan'])
            ->with('status', 'Aturan jadwal berhasil ditambahkan.');
    }

    /**
     * Formulir untuk mengubah aturan jadwal mingguan.
     */
    public function editAturan(ScheduleRule $scheduleRule): View
    {
        return view('admin.manajemen-jadwal-aturan-form', [
            'scheduleRule' => $scheduleRule,
        ] + $this->referensiForm());
    }

    /**
     * Simpan perubahan aturan jadwal mingguan.
     */
    public function updateAturan(Request $request, ScheduleRule $scheduleRule): RedirectResponse
    {
        $scheduleRule->update($this->validatedAturan($request));

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'aturan'])
            ->with('status', 'Aturan jadwal berhasil diperbarui.');
    }

    /**
     * Hapus aturan jadwal mingguan.
     *
     * Sesi masa depan ikut dihapus supaya jadwal yang salah bisa dibersihkan,
     * sedangkan sesi yang sudah lewat tetap disimpan sebagai riwayat beserta
     * presensinya. Sesi yang sudah punya presensi tidak boleh dihapus karena
     * presensistored-nya akan hilang.
     */
    public function destroyAturan(ScheduleRule $scheduleRule): RedirectResponse
    {
        $sesiMendatang = $scheduleRule->classSessions()
            ->whereDate('session_date', '>=', Carbon::today()->toDateString())
            ->get();

        $sesiBerpresensi = $sesiMendatang->filter(
            fn (ClassSession $sesi) => $sesi->attendance()->exists() || $sesi->tutorAttendance()->exists()
        );

        if ($sesiBerpresensi->isNotEmpty()) {
            return back()->withErrors([
                'aturan' => $sesiBerpresensi->count().' sesi yang akan datang sudah tercatat presensinya. '
                    .'Batalkan sesinya satu per satu bila tidak wanted, jangan hapus aturannya.',
            ]);
        }

        $jumlahMendatang = $sesiMendatang->count();
        $jumlahLampau = $scheduleRule->classSessions()->count() - $jumlahMendatang;

        $sesiMendatang->each(fn (ClassSession $sesi) => $sesi->delete());
        $scheduleRule->delete();

        $pesan = 'Aturan jadwal berhasil dihapus.';

        if ($jumlahMendatang > 0) {
            $pesan .= ' '.$jumlahMendatang.' sesi yang akan datang ikut dihapus.';
        }

        if ($jumlahLampau > 0) {
            $pesan .= ' '.$jumlahLampau.' sesi lampau dipertahankan beserta presensinya.';
        }

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'aturan'])
            ->with('status', $pesan);
    }

    /**
     * Bangun sesi terjadwal dari aturan mingguan untuk rentang tanggal tertentu.
     *
     * Permintaan dengan tombol "simulasi" hanya menghitung dampaknya tanpa
     * menyimpan sesi baru, supaya admin bisa melihat dulu berapa sesi yang
     * akan dibuat sebelum benar-benar dijalankan.
     */
    public function generate(Request $request, JadwalSesiService $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'class_group_ids' => ['nullable', 'array'],
            'class_group_ids.*' => ['integer', 'exists:class_groups,id'],
        ], [
            'to_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $dari = Carbon::parse($validated['from_date'])->startOfDay();
        $sampai = Carbon::parse($validated['to_date'])->endOfDay();

        if ($dari->diffInDays($sampai) > self::MAKS_RENTANG_GENERATE) {
            return back()
                ->withInput()
                ->withErrors([
                    'to_date' => 'Rentang generate maksimal '.self::MAKS_RENTANG_GENERATE.' hari (1 kuartal). '
                        .'pecah jadwalnya menjadi beberapa periode.',
                ]);
        }

        $simulasi = $request->boolean('preview');
        $ringkasan = $jadwal->generateSessions(
            $dari,
            $sampai,
            $validated['class_group_ids'] ?? null,
            $simulasi,
        );

        $parameter = [
            'tab' => 'sesi',
            'dari' => $validated['from_date'],
            'sampai' => $validated['to_date'],
        ];

        if ($simulasi) {
            return redirect()
                ->route('admin.manajemenJadwal', $parameter)
                ->with('simulasi', $ringkasan);
        }

        if ($ringkasan['dibuat'] === 0) {
            return redirect()
                ->route('admin.manajemenJadwal', $parameter)
                ->with('generate', $ringkasan)
                ->with('error', $ringkasan['alasan_kosong'] ?? 'Tidak ada sesi yang bisa dibuat dari aturan jadwal.');
        }

        return redirect()
            ->route('admin.manajemenJadwal', $parameter)
            ->with('generate', $ringkasan)
            ->with('status', $ringkasan['dibuat'].' sesi terjadwal dibuat dari aturan jadwal.');
    }

    /**
     * Formulir untuk mengubah satu sesi terjadwal.
     */
    public function editSesi(ClassSession $classSession): View
    {
        return view('admin.manajemen-jadwal-sesi-form', [
            'classSession' => $classSession,
        ] + $this->referensiForm());
    }

    /**
     * Simpan perubahan satu sesi terjadwal.
     */
    public function updateSesi(Request $request, ClassSession $classSession): RedirectResponse
    {
        $classSession->update($this->validatedSesi($request));

        return $this->redirectKeSesi($request, 'Sesi pada '.$classSession->tanggal->format('d/m/Y').' berhasil diperbarui.');
    }

    /**
     * Batalkan satu sesi terjadwal.
     */
    public function batalkanSesi(Request $request, ClassSession $classSession): RedirectResponse
    {
        $classSession->update(['status' => 'dibatalkan']);

        return $this->redirectKeSesi($request, 'Sesi pada '.$classSession->tanggal->format('d/m/Y').' dibatalkan.');
    }

    /**
     * Kembali ke daftar sesi dengan rentang tanggal yang sama.
     */
    private function redirectKeSesi(Request $request, string $pesan): RedirectResponse
    {
        $parameter = ['tab' => 'sesi'];

        foreach (['dari', 'sampai'] as $key) {
            $nilai = $request->input($key);

            if (is_string($nilai) && Carbon::hasFormat($nilai, 'Y-m-d')) {
                $parameter[$key] = $nilai;
            }
        }

        return redirect()->route('admin.manajemenJadwal', $parameter)->with('status', $pesan);
    }

    /**
     * Simpan ruang belajar baru.
     */
    public function storeRuang(Request $request): RedirectResponse
    {
        Room::create($this->validatedRuang($request));

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'ruang'])
            ->with('status', 'Ruang belajar berhasil ditambahkan.');
    }

    /**
     * Simpan perubahan data ruang belajar.
     */
    public function updateRuang(Request $request, Room $room): RedirectResponse
    {
        $room->update($this->validatedRuang($request, $room));

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'ruang'])
            ->with('status', 'Ruang '.$room->name.' berhasil diperbarui.');
    }

    /**
     * Hapus ruang belajar yang belum dipakai sesi apa pun.
     */
    public function destroyRuang(Room $room): RedirectResponse
    {
        if ($room->classSessions()->exists()) {
            return back()->withErrors(['ruang' => 'Ruang '.$room->name.' masih dipakai sesi terjadwal.']);
        }

        $room->delete();

        return redirect()
            ->route('admin.manajemenJadwal', ['tab' => 'ruang'])
            ->with('status', 'Ruang belajar berhasil dihapus.');
    }

    /**
     * Opsi referensi yang dipakai kedua formulir.
     *
     * @return array<string, mixed>
     */
    private function referensiForm(): array
    {
        return [
            'kelas' => ClassGroup::orderBy('code')->get(),
            'mapel' => Subject::orderBy('name')->get(),
            'tentor' => StaffProfile::active()->orderBy('full_name_with_title')->get(),
            'ruangan' => Room::active()->orderBy('name')->get(),
            'shift' => StudyShift::query()->orderBy('starts_at')->get(),
        ];
    }

    /**
     * Filter status sesi dari query string.
     */
    private function statusDariRequest(Request $request): ?string
    {
        $status = $request->query('status');

        return in_array($status, ['terjadwal', 'selesai', 'dibatalkan'], true) ? $status : null;
    }

    /**
     * Rentang tanggal aktif dari query string, defaultnya bulan berjalan.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rentangDariRequest(Request $request): array
    {
        $dari = $this->tanggalDariQuery($request, 'dari') ?? Carbon::today()->startOfMonth();
        $sampai = $this->tanggalDariQuery($request, 'sampai') ?? Carbon::today()->endOfMonth();

        if ($dari->greaterThan($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()];
    }

    /**
     * Tanggal dari query string dengan format Y-m-d.
     */
    private function tanggalDariQuery(Request $request, string $key): ?Carbon
    {
        $nilai = $request->query($key);

        if (is_string($nilai) && Carbon::hasFormat($nilai, 'Y-m-d')) {
            return Carbon::parse($nilai);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAturan(Request $request): array
    {
        $validated = $request->validate([
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'staff_id' => ['required', 'exists:staff_profiles,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'study_shift_id' => ['nullable', 'exists:study_shifts,id'],
            'weekday' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $validated['start_time'] = $validated['start_time'].':00';
        $validated['end_time'] = $validated['end_time'].':00';

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedRuang(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('rooms', 'name')->ignore($room?->id),
            ],
            'floor' => ['nullable', 'string', 'max:10'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedSesi(Request $request): array
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'staff_id' => ['required', 'exists:staff_profiles,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'study_shift_id' => ['nullable', 'exists:study_shifts,id'],
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'topic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['terjadwal', 'berlangsung', 'selesai', 'dibatalkan'])],
        ], [
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $validated['start_time'] = $validated['start_time'].':00';
        $validated['end_time'] = $validated['end_time'].':00';

        return $validated;
    }
}
