<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\ScheduleRule;
use App\Models\StudentAttendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Mengelola sesi terjadwal: membangun sesi dari aturan jadwal mingguan dan
 * menyediakan data ringkasan untuk halaman operasional admin.
 */
class JadwalSesiService
{
    /**
     * Bangun sesi terjadwal dari aturan jadwal mingguan dalam rentang tanggal.
     *
     * Aturan yang sudah punya sesi untuk tanggal yang sama dilewati agar
     * proses generate bisa dijalankan berulang kali tanpa menggandakan data.
     * Aturan yang bentrok dengan sesi lain pada jam yang bertumpuk (tentor
     * atau ruang yang sama) juga dilewati.
     *
     * @param  array<int, int>|null  $classGroupIds  Batasi ke beberapa rombel, null berarti semua.
     * @param  bool  $dryRun  Hitung saja tanpa menyimpan sesi baru.
     * @return array{dibuat: int, dilewati: int, bentrok: int, detail: array<int, string>, tanggal_dibuat: array<int, string>, alasan_kosong: ?string, butuh_aturan: bool}
     */
    public function generateSessions(Carbon $from, Carbon $to, ?array $classGroupIds = null, bool $dryRun = false): array
    {
        $ringkasan = [
            'dibuat' => 0,
            'dilewati' => 0,
            'bentrok' => 0,
            'detail' => [],
            'tanggal_dibuat' => [],
            'alasan_kosong' => null,
            'butuh_aturan' => false,
        ];

        $rules = ScheduleRule::query()
            ->when($classGroupIds, fn ($query) => $query->whereIn('class_group_id', $classGroupIds))
            ->with(['classGroup', 'staff', 'room'])
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get();

        if ($rules->isEmpty()) {
            $ringkasan['alasan_kosong'] = 'Belum ada aturan jadwal mingguan. Buat aturan di tab Aturan Mingguan terlebih dahulu.';
            $ringkasan['butuh_aturan'] = true;

            return $ringkasan;
        }

        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $sessions = ClassSession::query()
            ->with(['staff', 'room'])
            ->whereBetween('session_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $terisi = [];
        foreach ($sessions as $session) {
            if ($session->schedule_rule_id) {
                $terisi[$session->schedule_rule_id.'|'.$session->tanggal->toDateString()] = true;
            }
        }

        $perTanggal = $sessions->groupBy(fn (ClassSession $session) => $session->tanggal->toDateString());

        $tanggal = $from->copy();

        while ($tanggal->lessThanOrEqualTo($to)) {
            foreach ($rules->where('weekday', $tanggal->isoWeekday()) as $rule) {
                $kunci = $rule->id.'|'.$tanggal->toDateString();

                if (isset($terisi[$kunci])) {
                    $ringkasan['dilewati']++;
                    $ringkasan['detail'][] = $tanggal->locale('id')->translatedFormat('l, d/m/Y').' · '.($rule->classGroup?->code ?? '-')
                        .' — sesi sudah pernah dibuat.';

                    continue;
                }

                $sesiTanggal = $this->koleksiSesiTanggal($perTanggal, $tanggal->toDateString());
                $bentrok = $this->cariBentrok($rule, $sesiTanggal);

                if ($bentrok !== null) {
                    $ringkasan['bentrok']++;
                    $ringkasan['detail'][] = $tanggal->locale('id')->translatedFormat('l, d/m/Y').' · '.($rule->classGroup?->code ?? '-')
                        .' · '.$this->rentangJam($rule).' — bentrok '.$bentrok;

                    continue;
                }

                $terisi[$kunci] = true;

                if (! $dryRun) {
                    $sesiBaru = ClassSession::create([
                        'schedule_rule_id' => $rule->id,
                        'class_group_id' => $rule->class_group_id,
                        'subject_id' => $rule->subject_id,
                        'staff_id' => $rule->staff_id,
                        'room_id' => $rule->room_id,
                        'study_shift_id' => $rule->study_shift_id,
                        'session_date' => $tanggal->toDateString(),
                        'start_time' => $rule->start_time,
                        'end_time' => $rule->end_time,
                        'status' => 'terjadwal',
                    ]);

                    $sesiTanggal->push($sesiBaru);
                }

                $ringkasan['dibuat']++;
                $ringkasan['tanggal_dibuat'][] = $tanggal->toDateString();
            }

            $tanggal->addDay();
        }

        if ($ringkasan['dibuat'] === 0 && $ringkasan['alasan_kosong'] === null) {
            $ringkasan['alasan_kosong'] = $ringkasan['bentrok'] > 0
                ? 'Semua sesi dilewati karena bentrok jadwal. Periksa rinciannya di bawah.'
                : 'Semua sesi sudah pernah dibuat pada rentang tanggal ini.';
        }

        return $ringkasan;
    }

    /**
     * Rentang jam aturan dalam format ringkas.
     */
    private function rentangJam(ScheduleRule $rule): string
    {
        return substr((string) $rule->start_time, 0, 5).'-'.substr((string) $rule->end_time, 0, 5);
    }

    /**
     * Kumpulan sesi pada satu tanggal, dibuat bila belum ada di hasil group.
     *
     * @param  Collection<string, Collection<int, ClassSession>>  $perTanggal
     * @return Collection<int, ClassSession>
     */
    private function koleksiSesiTanggal(Collection $perTanggal, string $tanggal): Collection
    {
        if (! $perTanggal->has($tanggal)) {
            $perTanggal->put($tanggal, collect());
        }

        return $perTanggal->get($tanggal);
    }

    /**
     * Seluruh sesi pada satu tanggal beserta relasi yang dibutuhkan tabel jadwal.
     *
     * @return Collection<int, ClassSession>
     */
    public function sesiPadaTanggal(Carbon $tanggal): Collection
    {
        return ClassSession::with(['classGroup', 'subject', 'staff', 'room', 'studyShift', 'tutorAttendance'])
            ->whereDate('session_date', $tanggal->toDateString())
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Seluruh sesi dalam rentang tanggal beserta relasi yang dibutuhkan tabel.
     *
     * @param  string|null  $status  Semua, terjadwal, selesai, atau dibatalkan.
     * @return LengthAwarePaginator<int, ClassSession>
     */
    public function sesiDalamRentang(Carbon $dari, Carbon $sampai, ?string $status = null, int $perHalaman = 50): LengthAwarePaginator
    {
        return ClassSession::with(['classGroup', 'subject', 'staff', 'room', 'studyShift'])
            ->whereBetween('session_date', [$dari->toDateString(), $sampai->toDateString()])
            ->when($status, fn ($query) => $this->filterStatus($query, $status))
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->paginate($perHalaman)
            ->withQueryString();
    }

    /**
     * Filter status sesi dengan memperhitungkan status efektif.
     *
     * Sesi berstatus "terjadwal" yang tanggalnya sudah lewat dihitung sebagai
     * selesai supaya filter sesuai dengan yang tampil di layar.
     */
    private function filterStatus(Builder $query, string $status): Builder
    {
        $hariIni = Carbon::today()->toDateString();

        return match ($status) {
            'terjadwal' => $query->where('status', 'terjadwal')->where('session_date', '>=', $hariIni),
            'selesai' => $query->where(function (Builder $inner) use ($hariIni) {
                $inner->where('status', 'selesai')
                    ->orWhere(function (Builder $lama) use ($hariIni) {
                        $lama->where('status', 'terjadwal')->where('session_date', '<', $hariIni);
                    });
            }),
            'dibatalkan' => $query->where('status', 'dibatalkan'),
            default => $query,
        };
    }

    /**
     * Kepadatan jadwal dan tingkat kehadiran untuk tujuh hari dalam seminggu.
     *
     * @return array<int, array{hari: string, tanggal: string, sesi: int, hadir: int, total: int, rate: float|null, hari_ini: bool}>
     */
    public function kepadatanPekan(Carbon $tanggal): array
    {
        $awal = $tanggal->copy()->startOfWeek();
        $akhir = $awal->copy()->endOfWeek();

        $sesi = ClassSession::query()
            ->whereBetween('session_date', [$awal->toDateString(), $akhir->toDateString()])
            ->where('status', '!=', 'dibatalkan')
            ->get(['id', 'session_date']);

        $absensi = StudentAttendance::query()
            ->join('class_sessions', 'class_sessions.id', '=', 'student_attendance.class_session_id')
            ->whereBetween('class_sessions.session_date', [$awal->toDateString(), $akhir->toDateString()])
            ->where('class_sessions.status', '!=', 'dibatalkan')
            ->get(['class_sessions.session_date', 'student_attendance.status'])
            ->map(fn ($row) => [
                'tanggal' => Carbon::parse($row->session_date)->toDateString(),
                'status' => $row->status,
            ]);

        $baris = [];

        for ($hari = 0; $hari < 7; $hari++) {
            $tanggalHari = $awal->copy()->addDays($hari);
            $kunci = $tanggalHari->toDateString();

            $hadir = $absensi
                ->filter(fn (array $row) => $row['tanggal'] === $kunci && $row['status'] === 'hadir')
                ->count();
            $total = $absensi->filter(fn (array $row) => $row['tanggal'] === $kunci)->count();

            $baris[] = [
                'hari' => $tanggalHari->locale('id')->translatedFormat('l'),
                'tanggal' => $kunci,
                'sesi' => $sesi->filter(fn (ClassSession $session) => $session->tanggal->toDateString() === $kunci)->count(),
                'hadir' => $hadir,
                'total' => $total,
                'rate' => $total > 0 ? round($hadir / $total * 100, 1) : null,
                'hari_ini' => $kunci === $tanggal->toDateString(),
            ];
        }

        return $baris;
    }

    /**
     * Kunci shift sesi: "sore" atau "malam".
     *
     * Mengutamakan nama shift yang sudah ditata, lalu jatuh ke aturan jam
     * mulai sesi seperti yang dipakai dashboard admin.
     */
    public function shiftKey(ClassSession $session): string
    {
        $nama = strtolower((string) ($session->studyShift?->name ?? ''));

        if (str_contains($nama, 'malam')) {
            return 'malam';
        }

        if (str_contains($nama, 'sore')) {
            return 'sore';
        }

        return $this->jamMulai($session->start_time) < 16 ? 'sore' : 'malam';
    }

    /**
     * Jam mulai sesi dalam bentuk jam integer.
     */
    private function jamMulai(?string $time): int
    {
        if (! $time) {
            return 0;
        }

        return (int) explode(':', $time)[0];
    }

    /**
     * Cari bentrok jam antara aturan dan sesi yang sudah ada pada tanggal sama.
     */
    private function cariBentrok(ScheduleRule $rule, Collection $sessions): ?string
    {
        foreach ($sessions as $session) {
            if ($session->status === 'dibatalkan') {
                continue;
            }

            if ($session->start_time >= $rule->end_time || $session->end_time <= $rule->start_time) {
                continue;
            }

            if ((int) $session->staff_id === (int) $rule->staff_id) {
                return 'tentor '.$session->staff?->full_name_with_title;
            }

            if ($rule->room_id !== null && (int) $session->room_id === (int) $rule->room_id) {
                return 'ruang '.$session->room?->name;
            }
        }

        return null;
    }
}
