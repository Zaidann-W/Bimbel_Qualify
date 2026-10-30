<?php

namespace App\Services;

use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\StaffProfile;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnerLaporanAbsensiService
{
    public function rekap(?string $periode = null, ?string $jenjang = null): array
    {
        $range = $this->resolveRange($periode);
        $start = $range['start'];
        $end = $range['end'];

        $classGroups = $this->getClassGroups($jenjang, $start, $end);

        $rekapJenjang = $classGroups->map(function ($kelas) use ($start, $end) {
            return $this->rekapKelas($kelas, $start, $end);
        })->filter()->values();

        $rekapTentor = $this->rekapTentor($start, $end);

        $summary = $this->summary($rekapJenjang, $rekapTentor, $start, $end, $classGroups->pluck('id'));

        return [
            'summary' => $summary,
            'rekapJenjang' => $rekapJenjang->toArray(),
            'rekapTentor' => $rekapTentor->toArray(),
            'filters' => [
                'periode' => $range['label'],
                'jenjang' => $jenjang ?? 'Semua',
            ],
        ];
    }

    private function resolveRange(?string $periode): array
    {
        $now = Carbon::now();
        $label = $periode ?? 'Bulan Ini';

        if ($periode === 'TA' || $periode === 'Tahun Ajaran') {
            return [
                'start' => $now->copy()->startOfYear()->subMonths(6),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'Tahun Ajaran '.$now->year.'/'.($now->year + 1),
            ];
        }

        $bulanMap = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        if ($periode && preg_match('/^(\w+)\s+(\d{4})$/', $periode, $m)) {
            $bln = $bulanMap[$m[1]] ?? $now->month;
            $thn = (int) $m[2];
            $start = Carbon::create($thn, $bln, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $label = $m[1].' '.$m[2];
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $label = 'Bulan Ini ('.$now->locale('id')->translatedFormat('F Y').')';
        }

        return ['start' => $start, 'end' => $end, 'label' => $label];
    }

    private function getClassGroups(?string $jenjang, Carbon $start, Carbon $end)
    {
        $query = ClassGroup::with(['academicPeriod', 'scheduleRules.subject', 'scheduleRules.staff'])
            ->whereHas('scheduleRules', function ($q) use ($start, $end) {
                $q->whereHas('classSessions', function ($sq) use ($start, $end) {
                    $sq->whereDate('session_date', '>=', $start)
                        ->whereDate('session_date', '<=', $end)
                        ->where('status', '!=', 'dibatalkan');
                });
            });

        if ($jenjang && $jenjang !== 'Semua') {
            if (str_contains(strtoupper($jenjang), 'SMA')) {
                $query->whereIn('education_level', ['SMA']);
            } elseif (str_contains(strtoupper($jenjang), 'SMP')) {
                $query->where('education_level', 'SMP');
            } elseif (str_contains(strtoupper($jenjang), 'INTENSIF') || str_contains(strtoupper($jenjang), 'GARANSI')) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%INTENSIF%')
                        ->orWhere('code', 'like', '%GARANSI%');
                });
            }
        }

        return $query->get();
    }

    private function rekapKelas(ClassGroup $kelas, Carbon $start, Carbon $end): ?array
    {
        $sessions = ClassSession::where('class_group_id', $kelas->id)
            ->whereDate('session_date', '>=', $start)
            ->whereDate('session_date', '<=', $end)
            ->where('status', '!=', 'dibatalkan')
            ->get();

        if ($sessions->isEmpty()) {
            return null;
        }

        $studentIds = Enrollment::where('class_group_id', $kelas->id)
            ->where('status', 'aktif')
            ->pluck('student_id');

        if ($studentIds->isEmpty()) {
            return null;
        }

        $totalSesi = $sessions->count();
        $attendances = StudentAttendance::whereIn('class_session_id', $sessions->pluck('id'))
            ->whereIn('student_id', $studentIds)
            ->get();

        $hadir = $attendances->where('status', 'hadir')->count();
        $izin = $attendances->whereIn('status', ['izin', 'sakit'])->count();
        $alpa = $attendances->where('status', 'alpa')->count();

        $rate = ($hadir + $izin) > 0 ? round($hadir / ($hadir + $izin + $alpa) * 100, 1) : 0;
        $status = $rate >= 95 ? 'TINGGI' : ($rate >= 85 ? 'BAIK' : 'PERLU PERHATIAN');

        $kode = $kelas->code;
        $avatarClass = match (true) {
            str_contains($kode, '12') && str_contains($kode, 'S') => 'bg-gold text-espresso',
            str_contains($kode, 'FK') => 'bg-sandgold text-bronze',
            str_contains($kode, '11') => 'bg-sand text-muted',
            str_contains($kode, '10') => 'bg-sand text-muted',
            default => 'bg-sandgold text-bronze',
        };

        return [
            'kode' => $kode,
            'nama' => $kelas->education_level.' '.$kelas->grade.' '.($kelas->major ?? 'Reguler'),
            'rincian' => $this->getRincian($kelas),
            'siswa' => $studentIds->count(),
            'pertemuan' => $totalSesi,
            'hadir' => $hadir,
            'izin' => $izin,
            'alpa' => $alpa,
            'rate' => $rate,
            'status' => $status,
            'avatar' => $avatarClass,
        ];
    }

    private function getRincian(ClassGroup $kelas): string
    {
        $subjects = $kelas->scheduleRules->pluck('subject.name')->unique()->filter()->implode(', ');
        $shift = $kelas->scheduleRules->first()?->studyShift->name ?? '';

        $parts = [];
        if ($subjects) {
            $parts[] = $subjects;
        }
        if ($shift) {
            $parts[] = $shift;
        }

        return $parts ? implode(' • ', $parts) : 'Kurikulum Standar';
    }

    private function rekapTentor(Carbon $start, Carbon $end): Collection
    {
        $staffIds = StaffProfile::active()->pluck('id');

        $sessions = ClassSession::whereIn('staff_id', $staffIds)
            ->whereDate('session_date', '>=', $start)
            ->whereDate('session_date', '<=', $end)
            ->where('status', '!=', 'dibatalkan')
            ->get();

        return $staffIds->map(function ($staffId) use ($sessions) {
            $staff = StaffProfile::find($staffId);
            if (! $staff) {
                return null;
            }

            $staffSessions = $sessions->where('staff_id', $staffId);
            $totalSesi = $staffSessions->count();

            if ($totalSesi === 0) {
                return null;
            }

            $attendances = DB::table('tutor_attendance')
                ->whereIn('class_session_id', $staffSessions->pluck('id'))
                ->get();

            $hadir = $attendances->where('status', 'hadir')->count();
            $telat = $attendances->where('status', 'terlambat')->count();
            $tercatat = $attendances->count();

            $ketepatan = $totalSesi > 0 ? round($hadir / $totalSesi * 100, 1) : 0;
            $status = $tercatat === 0
                ? 'BELUM ADA DATA'
                : ($ketepatan >= 98 ? 'TEPAT' : ($ketepatan >= 90 ? 'BAIK' : 'PERLU PERHATIAN'));

            $avatarClass = match ($status) {
                'TEPAT' => 'bg-sandgold text-bronze',
                'BAIK' => 'bg-sand text-muted',
                'BELUM ADA DATA' => 'bg-sand text-muted',
                default => 'bg-ember-100 text-ember-600',
            };

            if ($hadir === $totalSesi) {
                $avatarClass = 'bg-gold text-espresso';
            }

            return [
                'inisial' => $this->initials($staff->full_name_with_title),
                'nama' => $staff->full_name_with_title,
                'rincian' => $this->tentorRincian($staffSessions),
                'sesi' => $totalSesi,
                'hadir' => $hadir,
                'telat' => $telat,
                'tercatat' => $tercatat,
                'ketepatan' => $tercatat === 0 ? '—' : number_format($ketepatan, 1, ',', '').'%',
                'ketepatanValue' => $ketepatan,
                'status' => $status,
                'avatar' => $avatarClass,
            ];
        })->filter()->sortByDesc('ketepatanValue')->values();
    }

    private function tentorRincian(Collection $sessions): string
    {
        $sesi = $sessions->first();
        $subject = $sesi?->subject?->name;
        $kelas = $sesi?->classGroup?->code;

        $parts = array_filter([$subject, $kelas]);

        return $parts ? implode(' • ', $parts) : 'Umum';
    }

    private function summary(Collection $rekapJenjang, Collection $rekapTentor, Carbon $start, Carbon $end, Collection $classGroupIds): array
    {
        $totalSesi = $rekapJenjang->sum('pertemuan');
        $totalHadir = $rekapJenjang->sum('hadir');
        $totalIzin = $rekapJenjang->sum('izin');
        $totalAlpa = $rekapJenjang->sum('alpa');

        $rateSiswa = ($totalHadir + $totalIzin + $totalAlpa) > 0
            ? round($totalHadir / ($totalHadir + $totalIzin + $totalAlpa) * 100, 1)
            : 0;

        $totalTentorSesi = $rekapTentor->sum('sesi');
        $totalTentorHadir = $rekapTentor->sum('hadir');
        $totalTentorTercatat = $rekapTentor->sum('tercatat');
        $rateTentor = $totalTentorSesi > 0 ? round($totalTentorHadir / $totalTentorSesi * 100, 1) : 0;

        $sessions = ClassSession::whereIn('class_group_id', $classGroupIds)
            ->whereDate('session_date', '>=', $start)
            ->whereDate('session_date', '<=', $end)
            ->where('status', '!=', 'dibatalkan');

        $soreCount = (clone $sessions)->whereTime('start_time', '<', '16:00:00')->count();
        $malamCount = (clone $sessions)->whereTime('start_time', '>=', '16:00:00')->count();

        $previousRate = $this->attendanceRateBetween(
            $start->copy()->subDays($start->diffInDays($end) + 1),
            $start->copy()->subDay(),
        );

        return [
            'siswaRate' => $rateSiswa,
            'siswaDetail' => number_format($totalHadir).' dari '.number_format($totalHadir + $totalIzin + $totalAlpa).' Sesi',
            'siswaDelta' => $previousRate === null ? null : round($rateSiswa - $previousRate, 1),
            'tentorRate' => $rateTentor,
            'tentorDetail' => $rekapTentor->count().' Tentor Aktif',
            'tentorStatus' => $totalTentorTercatat === 0 ? 'Belum Ada Data' : 'Optimal',
            'totalSesi' => $totalSesi,
            'soreCount' => $soreCount,
            'malamCount' => $malamCount,
            'periodeLabel' => $start->locale('id')->translatedFormat('F Y'),
        ];
    }

    private function attendanceRateBetween(Carbon $start, Carbon $end): ?float
    {
        $counts = StudentAttendance::query()
            ->join('class_sessions', 'class_sessions.id', '=', 'student_attendance.class_session_id')
            ->whereBetween('class_sessions.session_date', [$start->toDateString(), $end->toDateString()])
            ->where('class_sessions.status', '!=', 'dibatalkan')
            ->selectRaw("sum(case when student_attendance.status = 'hadir' then 1 else 0 end) as hadir")
            ->selectRaw('count(*) as total')
            ->first();

        if (! $counts || (int) $counts->total === 0) {
            return null;
        }

        return round((int) $counts->hadir / (int) $counts->total * 100, 1);
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->replaceMatches('/[.,]/', '')
            ->explode(' ')
            ->reject(fn (string $word): bool => in_array(Str::lower($word), ['kak', 'dr', 'drs', 'ir', 'prof'], true))
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
