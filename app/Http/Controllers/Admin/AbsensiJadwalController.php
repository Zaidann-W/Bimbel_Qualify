<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\LeaveRequest;
use App\Models\Room;
use App\Models\StudentAttendance;
use App\Models\StudyShift;
use App\Models\TutorAttendance;
use App\Services\JadwalSesiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AbsensiJadwalController extends Controller
{
    /**
     * Tampilkan jadwal sesi, presensi siswa/tentor, dan pengajuan izin.
     */
    public function index(Request $request, JadwalSesiService $jadwal): View
    {
        $tanggal = $this->tanggalDariRequest($request);

        $sesi = $jadwal->sesiPadaTanggal($tanggal);
        $sesiAktif = $sesi->where('status', '!=', 'dibatalkan');
        $sessionIds = $sesiAktif->pluck('id');

        $jumlahSore = $sesiAktif->filter(fn (ClassSession $item) => $jadwal->shiftKey($item) === 'sore')->count();
        $jumlahMalam = $sesiAktif->filter(fn (ClassSession $item) => $jadwal->shiftKey($item) === 'malam')->count();

        $pengajuan = $this->pengajuanIzin();

        return view('admin.absensi-jadwal', [
            'tanggal' => $tanggal,
            'jadwal' => $this->barisJadwal($sesi, $jadwal),
            'jumlahSore' => $jumlahSore,
            'jumlahMalam' => $jumlahMalam,
            'kpis' => [
                $this->kpiSesi($sesiAktif->count(), $jumlahSore, $jumlahMalam, $tanggal),
                $this->kpiPresensi($sessionIds, $sesiAktif->pluck('class_group_id')->unique()),
                $this->kpiTentor($sessionIds, $sesiAktif->count()),
                $this->kpiIzin($pengajuan->count()),
            ],
            'pengajuan' => $pengajuan,
            'ruangan' => $this->ketersediaanRuangan($sesi, $jadwal),
            'pekan' => $jadwal->kepadatanPekan($tanggal),
            'sesi_pil' => $this->referensiShift($jumlahSore, $jumlahMalam),
            'sesiBerikutnya' => $this->sesiBerikutnya($tanggal),
        ]);
    }

    /**
     * Sesi terjadwal terdekat setelah tanggal aktif.
     *
     * Dipakai supaya halaman tetap memberi tahu ada jadwal walaupun tanggal
     * aktif tidak memiliki sesi.
     */
    private function sesiBerikutnya(Carbon $tanggal): ?ClassSession
    {
        return ClassSession::with(['classGroup', 'subject', 'staff', 'room'])
            ->whereDate('session_date', '>', $tanggal->toDateString())
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();
    }

    /**
     * Tanggal aktif dari query string, default-nya hari ini.
     */
    private function tanggalDariRequest(Request $request): Carbon
    {
        $tanggal = $request->query('tanggal');

        if (is_string($tanggal) && Carbon::hasFormat($tanggal, 'Y-m-d')) {
            return Carbon::parse($tanggal)->startOfDay();
        }

        return Carbon::today();
    }

    /**
     * Baris tabel jadwal sesi beserta data tetangganya.
     *
     * @param  Collection<int, ClassSession>  $sesi
     * @return array<int, array<string, mixed>>
     */
    private function barisJadwal(Collection $sesi, JadwalSesiService $jadwal): array
    {
        return $sesi->map(function (ClassSession $item) use ($jadwal) {
            $checkIn = $item->tutorAttendance->firstWhere('status', '!=', 'tidak_hadir');

            return [
                'id' => $item->id,
                'sesi' => $jadwal->shiftKey($item),
                'waktu' => $this->waktu($item->start_time).' - '.$this->waktu($item->end_time),
                'mapel' => $item->subject?->name ?? 'Mata pelajaran',
                'tentor' => $item->staff?->full_name_with_title ?? '-',
                'inisial' => $this->inisial($item->staff?->full_name_with_title),
                'checkin' => $checkIn?->check_in_at?->format('H:i'),
                'rombel' => $item->classGroup?->code ?? '-',
                'ruang' => $item->room?->name ?? 'Tanpa ruang',
                'ruang_key' => (string) ($item->room_id ?? 'tanpa-ruang'),
                'status' => $item->status_efektif,
                'batal' => $item->status_efektif === 'dibatalkan',
            ];
        })->values()->all();
    }

    /**
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiSesi(int $total, int $sore, int $malam, Carbon $tanggal): array
    {
        return [
            'label' => $tanggal->isToday()
                ? 'Total Sesi Hari Ini'
                : 'Total Sesi '.$tanggal->translatedFormat('d/m/Y'),
            'nilai' => (string) $total,
            'satuan' => 'Kelas',
            'satuan_kelas' => 'text-muted',
            'ikon' => 'calendar',
            'tile' => 'bg-sandgold text-bronze',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => 'Sesi Sore: '.$sore,
            'catatan2' => 'Sesi Malam: '.$malam,
            'lencana' => null,
            'tautan' => null,
        ];
    }

    /**
     * @param  Collection<int, int>  $sessionIds
     * @param  Collection<int, int>  $classGroupIds  Rombel yang punya sesi pada tanggal aktif.
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiPresensi(Collection $sessionIds, Collection $classGroupIds): array
    {
        $absensi = StudentAttendance::query()
            ->whereIn('class_session_id', $sessionIds)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($absensi['hadir'] ?? 0);
        $total = (int) $absensi->sum();

        if ($total === 0) {
            return $this->kpiPresensiKosong($classGroupIds);
        }

        return [
            'label' => 'Presensi Siswa',
            'nilai' => round($hadir / $total * 100, 1).'%',
            'satuan' => '',
            'satuan_kelas' => '',
            'ikon' => 'users',
            'tile' => 'bg-palegold text-espresso',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => $hadir.' Hadir',
            'catatan2' => ($total - $hadir).' Tidak Hadir',
            'lencana' => null,
            'tautan' => null,
        ];
    }

    /**
     * Card presensi saat belum ada satu pun presensi yang tercatat.
     *
     * Menyebutkan jumlah siswa yang seharusnya absen supaya admin tahu
     * tinggal siapa yang belum absen, atau kalau rombelnya memang belum ada
     * siswa yang terdaftar.
     *
     * @param  Collection<int, int>  $classGroupIds
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiPresensiKosong(Collection $classGroupIds): array
    {
        $siswaAktif = Enrollment::query()
            ->whereIn('class_group_id', $classGroupIds)
            ->where('status', 'aktif')
            ->distinct()
            ->count('student_id');

        $catatan = $siswaAktif > 0
            ? '0 / '.$siswaAktif.' siswa belum absen'
            : 'Belum ada siswa enrolled di kelas ini';

        return $this->kpiKosong('Presensi Siswa', $catatan, 'users', 'bg-palegold text-espresso');
    }

    /**
     * @param  Collection<int, int>  $sessionIds
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiTentor(Collection $sessionIds, int $totalSesi): array
    {
        $absensi = TutorAttendance::query()
            ->whereIn('class_session_id', $sessionIds)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $hadir = (int) ($absensi['hadir'] ?? 0) + (int) ($absensi['terlambat'] ?? 0);

        if ($totalSesi === 0) {
            return [
                'label' => 'Kehadiran Tentor',
                'nilai' => '—',
                'satuan' => 'Lengkap',
                'satuan_kelas' => 'text-gold',
                'ikon' => 'user-check',
                'tile' => 'bg-gold text-espresso',
                'aksen' => 'border-t-espresso',
                'gelap' => true,
                'catatan' => 'Belum ada sesi terjadwal',
                'catatan2' => null,
                'lencana' => null,
                'tautan' => null,
            ];
        }

        if ($hadir === 0) {
            return [
                'label' => 'Kehadiran Tentor',
                'nilai' => '—',
                'satuan' => 'Lengkap',
                'satuan_kelas' => 'text-gold',
                'ikon' => 'user-check',
                'tile' => 'bg-gold text-espresso',
                'aksen' => 'border-t-espresso',
                'gelap' => true,
                'catatan' => 'Belum ada data check-in tentor',
                'catatan2' => null,
                'lencana' => null,
                'tautan' => null,
            ];
        }

        return [
            'label' => 'Kehadiran Tentor',
            'nilai' => $hadir.' / '.$totalSesi,
            'satuan' => 'Lengkap',
            'satuan_kelas' => 'text-gold',
            'ikon' => 'user-check',
            'tile' => 'bg-gold text-espresso',
            'aksen' => 'border-t-espresso',
            'gelap' => true,
            'catatan' => round($hadir / $totalSesi * 100).'% Mengajar',
            'catatan2' => null,
            'lencana' => $hadir === $totalSesi ? 'Tepat Waktu' : null,
            'tautan' => null,
        ];
    }

    /**
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiIzin(int $menunggu): array
    {
        return [
            'label' => 'Pengajuan Izin / Sakit',
            'nilai' => (string) $menunggu,
            'satuan' => 'Menunggu',
            'satuan_kelas' => 'text-bronze',
            'ikon' => 'mail',
            'tile' => 'bg-sandgold text-bronze',
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => $menunggu > 0 ? 'Perlu verifikasi admin' : 'Tidak ada pengajuan menunggu',
            'catatan2' => null,
            'lencana' => null,
            'tautan' => $menunggu > 0 ? 'Lihat Panel' : null,
        ];
    }

    /**
     * Kartu KPI tanpa angka karena belum ada data yang tercatat.
     *
     * @return array{label: string, nilai: string, catatan: string, catatan2: ?string, satuan: string, satuan_kelas: string, ikon: string, tile: string, aksen: string, gelap: bool, lencana: ?string, tautan: ?string}
     */
    private function kpiKosong(string $label, string $catatan, string $ikon, string $tile): array
    {
        return [
            'label' => $label,
            'nilai' => '—',
            'satuan' => '',
            'satuan_kelas' => '',
            'ikon' => $ikon,
            'tile' => $tile,
            'aksen' => 'border-t-gold',
            'gelap' => false,
            'catatan' => $catatan,
            'catatan2' => null,
            'lencana' => null,
            'tautan' => null,
        ];
    }

    /**
     * Pengajuan izin / sakit yang menunggu verifikasi.
     *
     * @return Collection<int, LeaveRequest>
     */
    private function pengajuanIzin(): Collection
    {
        return LeaveRequest::with(['student.guardians', 'student.enrollments.classGroup'])
            ->where('status', 'menunggu')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Status ketersediaan setiap ruang pada tanggal aktif.
     *
     * @param  Collection<int, ClassSession>  $sesi
     * @return Collection<int, array{nama: string, status: string, aktif: bool}>
     */
    private function ketersediaanRuangan(Collection $sesi, JadwalSesiService $jadwal): Collection
    {
        return Room::active()->orderBy('name')->get()->map(function (Room $room) use ($sesi, $jadwal) {
            $sesiRuang = $sesi->where('room_id', $room->id)->where('status', '!=', 'dibatalkan');
            $adaSore = $sesiRuang->contains(fn (ClassSession $item) => $jadwal->shiftKey($item) === 'sore');
            $adaMalam = $sesiRuang->contains(fn (ClassSession $item) => $jadwal->shiftKey($item) === 'malam');

            if ($adaSore) {
                return ['nama' => $room->name, 'status' => 'Sesi Sore Aktif', 'aktif' => true];
            }

            if ($adaMalam) {
                return ['nama' => $room->name, 'status' => 'Siap Sesi Malam', 'aktif' => false];
            }

            return ['nama' => $room->name, 'status' => 'Belum Terjadwal', 'aktif' => false];
        });
    }

    /**
     * Rentang jam untuk setiap shift belajar.
     *
     * @return array<string, array{label: string, rentang: string, judul: string, jumlah: int}>
     */
    private function referensiShift(int $jumlahSore, int $jumlahMalam): array
    {
        $waktu = [
            'sore' => ['mulai' => '15:30', 'selesai' => '17:30'],
            'malam' => ['mulai' => '18:30', 'selesai' => '20:30'],
        ];

        foreach (['Sesi Sore' => 'sore', 'Sesi Malam' => 'malam'] as $nama => $kunci) {
            $shift = StudyShift::where('name', $nama)->first();

            if ($shift) {
                $waktu[$kunci]['mulai'] = substr((string) $shift->starts_at, 0, 5);
                $waktu[$kunci]['selesai'] = substr((string) $shift->ends_at, 0, 5);
            }
        }

        return [
            'sore' => [
                'label' => 'Sesi Sore',
                'rentang' => '('.$waktu['sore']['mulai'].' - '.$waktu['sore']['selesai'].')',
                'judul' => 'Daftar Kelas — Sesi Sore ('.$waktu['sore']['mulai'].' - '.$waktu['sore']['selesai'].')',
                'jumlah' => $jumlahSore,
            ],
            'malam' => [
                'label' => 'Sesi Malam',
                'rentang' => '('.$waktu['malam']['mulai'].' - '.$waktu['malam']['selesai'].')',
                'judul' => 'Daftar Kelas — Sesi Malam ('.$waktu['malam']['mulai'].' - '.$waktu['malam']['selesai'].')',
                'jumlah' => $jumlahMalam,
            ],
        ];
    }

    /**
     * Jam dalam format H:i tanpa detik.
     */
    private function waktu(?string $time): string
    {
        return $time ? substr($time, 0, 5) : '-';
    }

    /**
     * Inisial nama untuk avatar lingkaran.
     */
    private function inisial(?string $nama): string
    {
        if (! $nama) {
            return '?';
        }

        $kata = collect(preg_split('/\s+/', trim($nama)))
            ->reject(fn ($item) => $item === '' || in_array($item, ['Dr.', 'Ir.', 'H.', 'S.Pd.', 'M.Pd.', 'M.Si.', 'S.Si.', 'S.Sos.', 'S.E.', 'B.Ed.'], true))
            ->take(2)
            ->map(fn ($item) => mb_strtoupper(mb_substr($item, 0, 1)))
            ->implode('');

        return $kata !== '' ? $kata : mb_strtoupper(mb_substr(trim($nama), 0, 1));
    }
}
