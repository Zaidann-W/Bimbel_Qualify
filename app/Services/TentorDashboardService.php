<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Models\StudySession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TentorDashboardService
{
    /**
     * Susun seluruh data dashboard tentor dari basis data.
     *
     * @return array{
     *     user: User|null,
     *     staffProfile: StaffProfile|null,
     *     jobTitle: string,
     *     stats: array<int, array<string, mixed>>,
     *     sessionsToday: Collection<int, array<string, mixed>>,
     *     qualifixTickets: Collection<int, array<string, mixed>>,
     *     totalTeachingHours: float,
     *     todayDate: string,
     *     todayTime: string
     * }
     */
    public function dashboard(?User $user): array
    {
        $staffProfile = $user !== null
            ? StaffProfile::where('user_id', $user->id)->first()
            : null;

        $tutorName = $staffProfile?->full_name_with_title ?? $user?->name ?? '';
        $jobTitle = $staffProfile?->job_title ?? $user?->name ?? 'Tentor';
        $today = today();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Sesi mengajar hari ini untuk tentor ini
        $sessionsToday = collect();
        if ($staffProfile !== null) {
            $classSessionsToday = ClassSession::with(['classGroup', 'subject', 'room', 'studyShift', 'attendance', 'tutorAttendance'])
                ->where('staff_id', $staffProfile->id)
                ->whereDate('session_date', $today)
                ->where('status', '!=', 'dibatalkan')
                ->orderBy('start_time')
                ->get();

            if ($classSessionsToday->isNotEmpty()) {
                $sessionsToday = $classSessionsToday;
            }
        }

        if ($sessionsToday->isEmpty()) {
            $sessionsToday = StudySession::query()
                ->when($tutorName !== '', fn ($q) => $q->where('tutor_name', 'like', '%'.$tutorName.'%'))
                ->whereDate('date', $today)
                ->orderBy('start_time')
                ->get();
        }

        // Sesi minggu ini untuk tentor ini
        $totalMinutes = 0;
        if ($staffProfile !== null) {
            $classSessionsThisWeek = ClassSession::query()
                ->where('staff_id', $staffProfile->id)
                ->whereBetween('session_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->where('status', '!=', 'dibatalkan')
                ->get();

            foreach ($classSessionsThisWeek as $cs) {
                if ($cs->start_time && $cs->end_time) {
                    $start = Carbon::parse($cs->session_date.' '.$cs->start_time);
                    $end = Carbon::parse($cs->session_date.' '.$cs->end_time);
                    $totalMinutes += $start->diffInMinutes($end);
                }
            }
        }

        if ($totalMinutes === 0) {
            $sessionsThisWeek = StudySession::query()
                ->when($tutorName !== '', fn ($q) => $q->where('tutor_name', 'like', '%'.$tutorName.'%'))
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
            $totalMinutes = $sessionsThisWeek->sum('duration_minutes');
        }

        // Statistik kehadiran hari ini
        $totalSessionsToday = $sessionsToday->count();
        $attendancePending = $this->countAttendancePending($sessionsToday);

        // Tiket Qualifix yang menunggu tanggapan tentor
        $qualifixTickets = $staffProfile !== null
            ? QualifixTicket::query()
                ->whereHas('staff', fn ($q) => $q->where('staff_profiles.id', $staffProfile->id))
                ->where('status', 'menunggu_tentor')
                ->with(['student', 'subject'])
                ->orderByDesc('submitted_at')
                ->get()
            : collect();

        // Total jam mengajar minggu ini
        $totalTeachingHours = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0.0;

        return [
            'user' => $user,
            'staffProfile' => $staffProfile,
            'jobTitle' => $jobTitle,
            'stats' => $this->statCards($totalSessionsToday, $attendancePending, $qualifixTickets->count(), $totalTeachingHours),
            'sessionsToday' => $sessionsToday->map(fn (ClassSession|StudySession $sesi) => $this->kartuSesi($sesi)),
            'qualifixTickets' => $qualifixTickets->map(fn (QualifixTicket $ticket) => $this->kartuKlinikPR($ticket)),
            'totalTeachingHours' => $totalTeachingHours,
            'todayDate' => $today->locale('id')->translatedFormat('j F Y'),
            'todayTime' => now()->format('H:i'),
        ];
    }

    /**
     * Hitung berapa sesi yang belum diinput absensinya sama sekali.
     */
    private function countAttendancePending(Collection $sessions): int
    {
        $pending = 0;

        foreach ($sessions as $sesi) {
            if ($sesi instanceof ClassSession) {
                if (! $sesi->attendance()->exists()) {
                    $pending++;
                }
            } elseif ($sesi instanceof StudySession) {
                if (! $sesi->attendances()->exists()) {
                    $pending++;
                }
            }
        }

        return $pending;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function statCards(int $totalSessions, int $attendancePending, int $menungguTanggapan, float $totalJam): array
    {
        return [
            [
                'label' => 'Kelas Hari Ini',
                'value' => (string) $totalSessions,
                'sub' => 'Sesi mengajar hari ini',
                'tone' => 'bg-bark-100 text-bark-600',
                'icon' => 'graduation-cap',
            ],
            [
                'label' => 'Status Absensi',
                'value' => (string) $attendancePending,
                'sub' => 'Belum diinput absensi',
                'tone' => $attendancePending > 0 ? 'bg-ember-50 text-ember-600' : 'bg-emerald-50 text-emerald-600',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Qualifix',
                'value' => (string) $menungguTanggapan,
                'sub' => 'Butuh respons Anda',
                'tone' => $menungguTanggapan > 0 ? 'bg-ember-50 text-ember-600' : 'bg-bark-50 text-bark-500',
                'icon' => 'message-circle',
            ],
            [
                'label' => 'Jam Mengajar Pekan Ini',
                'value' => number_format($totalJam, 1, '.', ',').' Jam',
                'sub' => 'Target: 16 Jam / Pekan',
                'tone' => 'bg-bark-50 text-bark-500',
                'icon' => 'clock',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function kartuSesi(ClassSession|StudySession $sesi): array
    {
        if ($sesi instanceof ClassSession) {
            $tanggal = $sesi->tanggal;
            $start = substr((string) $sesi->start_time, 0, 5);
            $end = substr((string) $sesi->end_time, 0, 5);
            $startFmt = str_replace(':', '.', $start);
            $endFmt = str_replace(':', '.', $end);

            $hasAttendance = $sesi->attendance()->exists();
            $enrollmentCount = Enrollment::where('class_group_id', $sesi->class_group_id)
                ->where('status', 'aktif')
                ->count();

            return [
                'id' => $sesi->id,
                'waktu' => sprintf('%s – %s WIB', $startFmt, $endFmt),
                'mapel' => $sesi->subject?->name ?? $sesi->topic ?? 'Mata Pelajaran',
                'desc' => $sesi->topic ? 'Topik: '.$sesi->topic : ($sesi->classGroup?->education_level.' '.$sesi->classGroup?->grade ?? '-'),
                'kelas' => $sesi->classGroup?->code ?? '-',
                'ruang' => $sesi->room?->name ?? '-',
                'siswa' => $enrollmentCount.' Siswa Terdaftar',
                'badge' => $hasAttendance ? 'Absensi Selesai' : ($tanggal->isToday() ? 'Akan Dimulai Segera' : null),
                'badgeTone' => $hasAttendance ? 'emerald' : 'amber',
                'actions' => [
                    [
                        'label' => $hasAttendance ? 'Lihat / Edit Absen' : 'Input Absen Kelas',
                        'icon' => 'clipboard-form',
                        'tone' => 'ember',
                        'url' => route('tentor.absensi', ['session_id' => $sesi->id]),
                        'href' => route('tentor.absensi', ['session_id' => $sesi->id]),
                    ],
                    [
                        'label' => 'Buka Modul Ajar',
                        'icon' => 'book-open',
                        'tone' => 'bark',
                        'url' => route('tentor.materi'),
                        'href' => route('tentor.materi'),
                    ],
                ],
            ];
        }

        $tanggal = Carbon::parse($sesi->date);

        return [
            'id' => $sesi->id,
            'waktu' => sprintf('%s – %s WIB', $sesi->start_time->format('H.i'), $sesi->end_time->format('H.i')),
            'mapel' => $sesi->title,
            'desc' => $sesi->description ?? '-',
            'kelas' => $sesi->category_badge ?? '-',
            'ruang' => $sesi->room_name ?? '-',
            'siswa' => $sesi->attendances()->count().' Siswa Terdaftar',
            'badge' => $tanggal->isToday() ? 'Akan Dimulai Segera' : null,
            'badgeTone' => 'amber',
            'actions' => [
                [
                    'label' => 'Input Absen Kelas',
                    'icon' => 'clipboard-form',
                    'tone' => 'ember',
                    'url' => route('tentor.absensi'),
                    'href' => route('tentor.absensi'),
                ],
                [
                    'label' => 'Buka Modul Ajar',
                    'icon' => 'book-open',
                    'tone' => 'bark',
                    'url' => route('tentor.materi'),
                    'href' => route('tentor.materi'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function kartuKlinikPR(QualifixTicket $ticket): array
    {
        $student = $ticket->student;
        $subject = $ticket->subject;

        return [
            'id' => $ticket->id,
            'initials' => $this->initials($student?->full_name ?? 'Siswa'),
            'nama' => $student?->full_name ?? 'Siswa',
            'status' => 'Menunggu',
            'meta' => ($student?->class_group_id !== null ? 'Kelas '.$student->class_group_id : '-').' • '.$ticket->submitted_at?->diffForHumans() ?? '',
            'topik' => $ticket->title,
            'preview' => $ticket->question,
            'tags' => [$subject?->name ?? 'Mata Pelajaran'],
            'active' => true,
        ];
    }

    private function initials(string $name): string
    {
        return Str::of($name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
