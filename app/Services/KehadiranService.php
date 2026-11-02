<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\SessionAttendance;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class KehadiranService
{
    /**
     * Susun seluruh data halaman kehadiran anak untuk orang tua/wali.
     *
     * Data diambil dari basis data berdasarkan user yang sedang login (role ortu)
     * → guardian → siswa terkait → presensi sesi belajar.
     *
     * @return array{
     *     guardian: array<string, mixed>|null,
     *     anak: array<int, array<string, mixed>>,
     *     stats: array<int, array<string, mixed>>,
     *     riwayat: array<int, array<string, mixed>>,
     *     terakhir: array<string, mixed>|null
     * }
     */
    public function kehadiran(): array
    {
        $user = Auth::user();
        $guardian = null;
        $anak = [];

        try {
            $guardian = Guardian::where('user_id', $user->id)->first();
        } catch (\Exception $e) {
            $guardian = null;
        }

        if ($guardian !== null) {
            $anak = $this->dataAnak($guardian);
        }

        $stats = $this->statCards($anak);
        $riwayat = $this->riwayatSemua($anak);
        $terakhir = $this->presensiTerakhir($anak);

        return [
            'guardian' => $guardian === null ? null : [
                'id' => $guardian->id,
                'name' => $guardian->name,
                'occupation' => $guardian->occupation,
                'phone_wa' => $guardian->phone_wa,
                'email' => $guardian->email,
                'address' => $guardian->address,
            ],
            'anak' => $anak,
            'stats' => $stats,
            'riwayat' => $riwayat,
            'terakhir' => $terakhir,
        ];
    }

    /**
     * Dapatkan semua siswa yang terkait dengan guardian ini beserta data presensinya.
     *
     * @return array<int, array<string, mixed>>
     */
    private function dataAnak(Guardian $guardian): array
    {
        $students = $guardian->students()->active()->get();

        return $students->map(fn (Student $siswa) => [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->full_name,
            'nama_panggilan' => $siswa->nickname ?? $siswa->full_name,
            'inisial' => $siswa->inisial(),
            'sekolah' => $siswa->school_name,
            'kelas' => trim(sprintf('%s %s %s', $siswa->school_grade, $siswa->education_level, $siswa->major)),
            'status' => $siswa->status,
            'email' => $siswa->email,
            'phone_wa' => $siswa->phone_wa,
            'presensi' => $this->presensiSiswa($siswa),
            'riwayat' => $this->riwayatSiswa($siswa),
        ])->toArray();
    }

    /**
     * Dapatkan statistik presensi per siswa.
     *
     * @return array<string, mixed>
     */
    private function presensiSiswa(Student $siswa): array
    {
        $totalSesi = SessionAttendance::where('student_id', $siswa->id)->count();
        $hadir = SessionAttendance::where('student_id', $siswa->id)
            ->where('attendance_status', SessionAttendance::STATUS_HADIR)
            ->count();
        $izin = SessionAttendance::where('student_id', $siswa->id)
            ->where('attendance_status', SessionAttendance::STATUS_IZIN)
            ->count();
        $sakit = SessionAttendance::where('student_id', $siswa->id)
            ->where('attendance_status', SessionAttendance::STATUS_SAKIT)
            ->count();
        $alpa = SessionAttendance::where('student_id', $siswa->id)
            ->where('attendance_status', SessionAttendance::STATUS_ALPHA)
            ->count();

        $persen = $totalSesi === 0 ? 0.0 : round($hadir / $totalSesi * 100, 1);

        return [
            'total_sesi' => $totalSesi,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'persen' => $persen,
        ];
    }

    /**
     * Dapatkan riwayat presensi per siswa dengan detail sesi belajar.
     *
     * @return array<int, array<string, mixed>>
     */
    private function riwayatSiswa(Student $siswa): array
    {
        return SessionAttendance::with('studySession')
            ->where('student_id', $siswa->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (SessionAttendance $presensi) => $this->formatRiwayat($presensi, $siswa))
            ->toArray();
    }

    /**
     * Dapatkan riwayat presensi gabungan semua anak, diurutkan tanggal terbaru.
     *
     * @return array<int, array<string, mixed>>
     */
    private function riwayatSemua(array $anak): array
    {
        $kumpulkan = [];

        foreach ($anak as $a) {
            foreach ($a['riwayat'] as $item) {
                $kumpulkan[] = $item;
            }
        }

        usort($kumpulkan, fn ($a, $b) => strtotime($b['tanggal'] ?? '1970-01-01') <=> strtotime($a['tanggal'] ?? '1970-01-01'));

        return array_slice($kumpulkan, 0, 20);
    }

    /**
     * Dapatkan presensi terakhir dari semua anak.
     *
     * @return array<string, mixed>|null
     */
    private function presensiTerakhir(array $anak): ?array
    {
        $terbaru = null;
        $terbaruTgl = null;

        foreach ($anak as $a) {
            foreach ($a['riwayat'] as $item) {
                $tgl = $item['tanggal'] ?? null;
                if ($tgl === null) {
                    continue;
                }
                $ts = strtotime($tgl);
                if ($terbaruTgl === null || $ts > $terbaruTgl) {
                    $terbaruTgl = $ts;
                    $terbaru = $item;
                }
            }
        }

        return $terbaru;
    }

    /**
     * Susun kartu statistik halaman kehadiran.
     *
     * @return array<int, array<string, mixed>>
     */
    private function statCards(array $anak): array
    {
        $jumlahAnak = count($anak);

        $totalSesi = array_sum(array_map(fn ($a) => (int) ($a['presensi']['total_sesi'] ?? 0), $anak));
        $totalHadir = array_sum(array_map(fn ($a) => (int) ($a['presensi']['hadir'] ?? 0), $anak));
        $totalIzin = array_sum(array_map(fn ($a) => (int) ($a['presensi']['izin'] ?? 0), $anak));
        $totalSakit = array_sum(array_map(fn ($a) => (int) ($a['presensi']['sakit'] ?? 0), $anak));
        $totalAlpa = array_sum(array_map(fn ($a) => (int) ($a['presensi']['alpa'] ?? 0), $anak));
        $persen = $totalSesi === 0 ? 0.0 : round($totalHadir / $totalSesi * 100, 1);

        return [
            [
                'label' => 'Kehadiran Fisik',
                'value' => (string) $totalHadir,
                'unit' => '/'.$totalSesi.' Sesi',
                'badge' => $persen >= 80 ? 'Sangat Disiplin' : null,
                'icon' => 'check-circle',
                'tone' => 'bg-emerald-50 text-emerald-600',
                'badgeTone' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'label' => 'Izin',
                'value' => (string) $totalIzin,
                'unit' => 'Sesi',
                'badge' => null,
                'icon' => 'info',
                'tone' => 'bg-amber-50 text-amber-600',
                'badgeTone' => null,
            ],
            [
                'label' => 'Sakit',
                'value' => (string) $totalSakit,
                'unit' => 'Sesi',
                'badge' => null,
                'icon' => 'help-circle',
                'tone' => 'bg-rose-50 text-rose-600',
                'badgeTone' => null,
            ],
            [
                'label' => 'Alpa / Tanpa Izin',
                'value' => (string) $totalAlpa,
                'unit' => 'Sesi',
                'badge' => null,
                'icon' => 'x',
                'tone' => 'bg-rose-100 text-rose-500',
                'badgeTone' => null,
            ],
            [
                'label' => 'Tingkat Kehadiran',
                'value' => number_format($persen, 1, '.', ',').'%',
                'unit' => $totalHadir.' dari '.$totalSesi.' sesi hadir',
                'badge' => null,
                'icon' => 'clock',
                'tone' => 'bg-bark-50 text-bark-400',
                'badgeTone' => null,
                'href' => null,
            ],
        ];
    }

    /**
     * Ubah satu record presensi menjadi array riwayat untuk tampilan tabel.
     *
     * @return array<string, mixed>
     */
    private function formatRiwayat(SessionAttendance $presensi, Student $siswa): array
    {
        $sesi = $presensi->studySession;
        $tanggal = $sesi?->date ?? $presensi->created_at;

        $statusLabel = match ($presensi->attendance_status) {
            SessionAttendance::STATUS_HADIR => 'Hadir',
            SessionAttendance::STATUS_IZIN => 'Izin',
            SessionAttendance::STATUS_SAKIT => 'Sakit',
            SessionAttendance::STATUS_ALPHA => 'Alpa',
            default => $presensi->attendance_status,
        };

        $statusTone = match ($presensi->attendance_status) {
            SessionAttendance::STATUS_HADIR => 'bg-emerald-50 text-emerald-600',
            SessionAttendance::STATUS_IZIN => 'bg-amber-50 text-amber-600',
            SessionAttendance::STATUS_SAKIT => 'bg-rose-50 text-rose-600',
            SessionAttendance::STATUS_ALPHA => 'bg-rose-100 text-rose-500',
            default => 'bg-bark-50 text-bark-500',
        };

        $masukTone = $presensi->attendance_status === SessionAttendance::STATUS_HADIR
            ? 'text-emerald-600'
            : 'text-bark-500';

        $masuk = $presensi->check_in_time?->format('H:i') !== null
            ? 'Masuk '.$presensi->check_in_time->format('H.i').' WIB'
            : ucfirst(strtolower($statusLabel));

        return [
            'anak' => $siswa->full_name,
            'nis' => $siswa->nis,
            'tanggal' => $tanggal?->format('Y-m-d') ?? '-',
            'tanggal_label' => $tanggal?->copy()->locale('id')->translatedFormat('l, j F Y') ?? '-',
            'masuk' => $masuk,
            'masuk_tone' => $masukTone,
            'mapel' => $sesi?->title ?? 'Mata Pelajaran',
            'tentor' => $sesi?->tutor_name ?? '-',
            'ruang' => $sesi?->room_name ?? '-',
            'status' => $statusLabel,
            'status_tone' => $statusTone,
            'catatan' => $presensi->permit_reason ?? '-',
            'check_in_time' => $presensi->check_in_time,
        ];
    }
}
