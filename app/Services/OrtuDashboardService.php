<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrtuDashboardService
{
    /**
     * Susun seluruh data dashboard ortu/wali dari basis data.
     *
     * Data diambil berdasarkan user yang sedang login (role ortu) → guardian → siswa terkait.
     *
     * @return array{
     *     guardian: array<string, mixed>|null,
     *     anak: array<int, array<string, mixed>>,
     *     stats: array<int, array<string, mixed>>,
     *     keuangan: array<string, mixed>,
     *     presensi: array<string, mixed>,
     *     jadwal: array<int, array<string, mixed>>,
     *     pengumuman: array<string, mixed>|null
     * }
     */
    public function dashboard(): array
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
        $keuangan = $this->keuangan($anak);
        $presensi = $this->presensi($anak);
        $jadwal = $this->jadwalPekan($anak);
        $pengumuman = $this->pengumuman();

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
            'keuangan' => $keuangan,
            'presensi' => $presensi,
            'jadwal' => $jadwal,
            'pengumuman' => $pengumuman,
        ];
    }

    /**
     * Dapatkan semua siswa yang terkait dengan guardian ini.
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
            'kelas_aktif' => $this->kelasAktif($siswa),
            'tagihan' => $this->tagihanSiswa($siswa),
            'presensi' => $this->presensiSiswa($siswa),
        ])->toArray();
    }

    /**
     * Dapatkan kelas aktif (enrollment) siswa.
     *
     * @return array<string, mixed>|null
     */
    private function kelasAktif(Student $siswa): ?array
    {
        $enrollment = Enrollment::where('student_id', $siswa->id)
            ->where('status', 'aktif')
            ->with('classGroup.academicPeriod')
            ->first();

        if ($enrollment === null || $enrollment->classGroup === null) {
            return null;
        }

        return [
            'id' => $enrollment->classGroup->id,
            'kode' => $enrollment->classGroup->code,
            'label' => trim(sprintf(
                'Kelas %s %s – %s',
                $enrollment->classGroup->grade ?? '',
                $enrollment->classGroup->education_level ?? '',
                $enrollment->classGroup->major ?? '',
            )),
            'periode' => $enrollment->classGroup->academicPeriod?->academic_year ?? '-',
            'semester' => $enrollment->classGroup->academicPeriod?->semester ?? '-',
        ];
    }

    /**
     * Dapatkan tagihan dan pembayaran siswa.
     *
     * @return array<string, mixed>
     */
    private function tagihanSiswa(Student $siswa): array
    {
        $invoices = Invoice::where('student_id', $siswa->id)
            ->with('academicPeriod')
            ->orderByDesc('due_date')
            ->get();

        $totalTagihan = $invoices->sum('amount');
        $totalTerbayar = $invoices->sum(function ($inv) {
            return $inv->payments()->where('status', 'berhasil')->sum('amount');
        });
        $lunas = $invoices->where('status', 'lunas')->count();
        $belumBayar = $invoices->where('status', 'belum_bayar')->count();
        $sebagian = $invoices->where('status', 'sebagian')->count();

        $tagihanTerdekat = $invoices->first();

        return [
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'sisa' => max(0, $totalTagihan - $totalTerbayar),
            'lunas' => $lunas,
            'belum_bayar' => $belumBayar,
            'sebagian' => $sebagian,
            'total_invoice' => $invoices->count(),
            'terdekat' => $tagihanTerdekat === null ? null : [
                'invoice_number' => $tagihanTerdekat->invoice_number,
                'description' => $tagihanTerdekat->description,
                'amount' => $tagihanTerdekat->amount,
                'due_date' => $tagihanTerdekat->due_date?->format('d M Y') ?? '-',
                'status' => $tagihanTerdekat->status,
                'periode' => $tagihanTerdekat->academicPeriod?->academic_year ?? '-',
            ],
        ];
    }

    /**
     * Dapatkan statistik presensi siswa.
     *
     * @return array<string, mixed>
     */
    private function presensiSiswa(Student $siswa): array
    {
        $totalSesi = StudentAttendance::where('student_id', $siswa->id)->count();
        $hadir = StudentAttendance::where('student_id', $siswa->id)
            ->where('status', 'hadir')
            ->count();
        $izin = StudentAttendance::where('student_id', $siswa->id)
            ->where('status', 'izin')
            ->count();
        $sakit = StudentAttendance::where('student_id', $siswa->id)
            ->where('status', 'sakit')
            ->count();
        $alpa = StudentAttendance::where('student_id', $siswa->id)
            ->where('status', 'alpa')
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
     * Susun kartu statistik dashboard.
     *
     * @return array<int, array<string, mixed>>
     */
    private function statCards(array $anak): array
    {
        $jumlahAnak = count($anak);
        $totalTagihan = array_sum(array_map(fn ($a) => (float) ($a['tagihan']['total_tagihan'] ?? 0), $anak));
        $totalTerbayar = array_sum(array_map(fn ($a) => (float) ($a['tagihan']['total_terbayar'] ?? 0), $anak));
        $totalSisa = array_sum(array_map(fn ($a) => (float) ($a['tagihan']['sisa'] ?? 0), $anak));
        $totalBelumBayar = array_sum(array_map(fn ($a) => (int) ($a['tagihan']['belum_bayar'] ?? 0), $anak));

        $totalSesi = array_sum(array_map(fn ($a) => (int) ($a['presensi']['total_sesi'] ?? 0), $anak));
        $totalHadir = array_sum(array_map(fn ($a) => (int) ($a['presensi']['hadir'] ?? 0), $anak));
        $persenPresensi = $totalSesi === 0 ? 0.0 : round($totalHadir / $totalSesi * 100, 1);

        return [
            [
                'label' => 'Anak Terdaftar',
                'value' => (string) $jumlahAnak,
                'sub' => 'Siswa aktif di Bimbel Qualify',
                'sub_tone' => 'text-bark-400 font-medium',
                'icon' => 'users',
                'icon_tone' => 'bg-bark-50 text-bark-400',
                'href' => null,
            ],
            [
                'label' => 'Total Tagihan',
                'value' => 'Rp '.number_format($totalTagihan, 0, ',', '.'),
                'sub' => 'Seluruh tagihan anak Anda',
                'sub_tone' => 'text-bark-400 font-medium',
                'icon' => 'credit-card',
                'icon_tone' => 'bg-bark-50 text-bark-400',
                'href' => route('ortu.pembayaran'),
            ],
            [
                'label' => 'Sudah Dibayar',
                'value' => 'Rp '.number_format($totalTerbayar, 0, ',', '.'),
                'sub' => 'Pembayaran yang telah diverifikasi',
                'sub_tone' => 'text-emerald-600 font-medium',
                'icon' => 'check-circle',
                'icon_tone' => 'bg-emerald-50 text-emerald-600',
                'href' => route('ortu.pembayaran'),
            ],
            [
                'label' => 'Belum Dibayar',
                'value' => 'Rp '.number_format($totalSisa, 0, ',', '.'),
                'sub' => $totalBelumBayar.' tagihan belum lunas',
                'sub_tone' => $totalBelumBayar > 0 ? 'text-ember-600 font-medium' : 'text-emerald-600 font-medium',
                'icon' => 'alert-circle',
                'icon_tone' => $totalBelumBayar > 0 ? 'bg-ember-50 text-ember-600' : 'bg-emerald-50 text-emerald-600',
                'href' => route('ortu.pembayaran'),
            ],
            [
                'label' => 'Tingkat Kehadiran',
                'value' => number_format($persenPresensi, 1, '.', ',').'%',
                'sub' => $totalHadir.' dari '.$totalSesi.' sesi hadir',
                'sub_tone' => 'text-bark-400 font-medium',
                'icon' => 'clock',
                'icon_tone' => 'bg-bark-50 text-bark-400',
                'href' => route('ortu.kehadiran'),
            ],
        ];
    }

    /**
     * Susun data keuangan ringkasan untuk dashboard.
     *
     * @return array<string, mixed>
     */
    private function keuangan(array $anak): array
    {
        $tagihanSemua = [];
        $pembayaranTerbaru = [];

        foreach ($anak as $a) {
            $inv = $a['tagihan'];
            $tagihanSemua[] = [
                'anak' => $a['nama'],
                'nis' => $a['nis'],
                'total_tagihan' => $inv['total_tagihan'],
                'total_terbayar' => $inv['total_terbayar'],
                'sisa' => $inv['sisa'],
                'lunas' => $inv['lunas'],
                'belum_bayar' => $inv['belum_bayar'],
                'terdekat' => $inv['terdekat'],
            ];

            if ($inv['terdekat'] !== null) {
                $pembayaranTerbaru[] = [
                    'anak' => $a['nama'],
                    'invoice_number' => $inv['terdekat']['invoice_number'],
                    'description' => $inv['terdekat']['description'],
                    'amount' => $inv['terdekat']['amount'],
                    'due_date' => $inv['terdekat']['due_date'],
                    'status' => $inv['terdekat']['status'],
                ];
            }
        }

        usort($pembayaranTerbaru, fn ($a, $b) => strtotime($b['due_date'] ?? '1970-01-01') <=> strtotime($a['due_date'] ?? '1970-01-01'));

        return [
            'total_tagihan' => array_sum(array_column($tagihanSemua, 'total_tagihan')),
            'total_terbayar' => array_sum(array_column($tagihanSemua, 'total_terbayar')),
            'total_sisa' => array_sum(array_column($tagihanSemua, 'sisa')),
            'tagihan_per_anak' => $tagihanSemua,
            'terdekat' => $pembayaranTerbaru,
        ];
    }

    /**
     * Susun data presensi ringkasan untuk dashboard.
     *
     * @return array<string, mixed>
     */
    private function presensi(array $anak): array
    {
        $totalSesi = array_sum(array_map(fn ($a) => (int) ($a['presensi']['total_sesi'] ?? 0), $anak));
        $totalHadir = array_sum(array_map(fn ($a) => (int) ($a['presensi']['hadir'] ?? 0), $anak));
        $totalIzin = array_sum(array_map(fn ($a) => (int) ($a['presensi']['izin'] ?? 0), $anak));
        $totalSakit = array_sum(array_map(fn ($a) => (int) ($a['presensi']['sakit'] ?? 0), $anak));
        $totalAlpa = array_sum(array_map(fn ($a) => (int) ($a['presensi']['alpa'] ?? 0), $anak));
        $persen = $totalSesi === 0 ? 0.0 : round($totalHadir / $totalSesi * 100, 1);

        $perAnak = array_map(fn ($a) => [
            'nama' => $a['nama'],
            'nis' => $a['nis'],
            'total_sesi' => $a['presensi']['total_sesi'],
            'hadir' => $a['presensi']['hadir'],
            'izin' => $a['presensi']['izin'],
            'sakit' => $a['presensi']['sakit'],
            'alpa' => $a['presensi']['alpa'],
            'persen' => $a['presensi']['persen'],
        ], $anak);

        return [
            'total_sesi' => $totalSesi,
            'hadir' => $totalHadir,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpa' => $totalAlpa,
            'persen' => $persen,
            'per_anak' => $perAnak,
        ];
    }

    /**
     * Dapatkan jadwal kelas pekan ini untuk semua anak.
     *
     * @return array<int, array<string, mixed>>
     */
    private function jadwalPekan(array $anak): array
    {
        $classGroupIds = array_unique(array_filter(array_map(fn ($a) => $a['kelas_aktif']['id'] ?? null, $anak)));

        if (empty($classGroupIds)) {
            return [];
        }

        $awalMinggu = now()->copy()->startOfWeek()->toDateString();
        $akhirMinggu = now()->copy()->endOfWeek()->toDateString();

        $sessions = ClassSession::with(['subject', 'staff', 'room'])
            ->whereIn('class_group_id', $classGroupIds)
            ->whereBetween('session_date', [$awalMinggu, $akhirMinggu])
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        return $sessions->map(fn (ClassSession $sesi) => $this->kartuJadwal($sesi))->toArray();
    }

    /**
     * Ubah ClassSession menjadi array kartu jadwal.
     *
     * @return array<string, mixed>
     */
    private function kartuJadwal(ClassSession $sesi): array
    {
        $tanggal = Carbon::parse($sesi->session_date);

        return [
            'hari' => Str::upper(substr($tanggal->locale('id')->translatedFormat('l'), 0, 3)),
            'tgl' => $tanggal->format('j'),
            'mapel' => $sesi->subject?->name ?? 'Mata Pelajaran',
            'topik' => $sesi->topic ?? '-',
            'waktu' => sprintf(
                '%s – %s WIB%s',
                $this->jam($sesi->start_time),
                $this->jam($sesi->end_time),
                $sesi->room?->name !== null ? ' • '.$sesi->room->name : '',
            ),
            'tentor' => $sesi->staff?->full_name_with_title ?? '-',
            'hari_ini' => $tanggal->isToday(),
            'tanggal' => $tanggal->format('Y-m-d'),
        ];
    }

    /**
     * Dapatkan pengumuman terbaru.
     *
     * @return array<string, mixed>|null
     */
    private function pengumuman(): ?array
    {
        try {
            $pengumuman = Announcement::query()
                ->whereNotNull('published_at')
                ->orderByDesc('published_at')
                ->first();

            if ($pengumuman === null) {
                return null;
            }

            return [
                'judul' => $pengumuman->title,
                'isi' => $pengumuman->content,
                'tanggal' => $pengumuman->published_at?->format('d M Y') ?? '-',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function jam(?string $waktu): string
    {
        return $waktu === null ? '-' : str_replace(':', '.', substr($waktu, 0, 5));
    }
}
