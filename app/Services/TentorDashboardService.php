<?php

namespace App\Services;

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
        $today = today();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Sesi mengajar hari ini untuk tentor ini
        $sessionsToday = StudySession::query()
            ->when($tutorName !== '', fn ($q) => $q->where('tutor_name', 'like', '%'.$tutorName.'%'))
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();

        // Sesi minggu ini untuk tentor ini
        $sessionsThisWeek = StudySession::query()
            ->when($tutorName !== '', fn ($q) => $q->where('tutor_name', 'like', '%'.$tutorName.'%'))
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Statistik kehadiran hari ini
        $totalSessionsToday = $sessionsToday->count();
        $attendancePending = $this->countAttendancePending($sessionsToday);

        // Tiket Klinik PR yang menunggu tanggapan tentor
        $qualifixTickets = $staffProfile !== null
            ? QualifixTicket::query()
                ->whereHas('staff', fn ($q) => $q->where('staff_profiles.id', $staffProfile->id))
                ->where('status', 'menunggu_tentor')
                ->with(['student', 'subject'])
                ->orderByDesc('submitted_at')
                ->get()
            : collect();

        // Total jam mengajar minggu ini
        $totalMinutes = $sessionsThisWeek->sum('duration_minutes');
        $totalTeachingHours = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0.0;

        return [
            'user' => $user,
            'staffProfile' => $staffProfile,
            'stats' => $this->statCards($totalSessionsToday, $attendancePending, $qualifixTickets->count(), $totalTeachingHours),
            'sessionsToday' => $sessionsToday->map(fn (StudySession $sesi) => $this->kartuSesi($sesi)),
            'qualifixTickets' => $qualifixTickets->map(fn (QualifixTicket $ticket) => $this->kartuKlinikPR($ticket)),
            'totalTeachingHours' => $totalTeachingHours,
            'todayDate' => $today->locale('id')->translatedFormat('l, j F Y'),
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
            $hasAttendance = $sesi->attendances()->exists();
            if (! $hasAttendance) {
                $pending++;
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
                'label' => 'Klinik PR Siswa',
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
    private function kartuSesi(StudySession $sesi): array
    {
        $tanggal = Carbon::parse($sesi->date);

        return [
            'waktu' => sprintf('%s – %s WIB', $sesi->start_time->format('H.i'), $sesi->end_time->format('H.i')),
            'mapel' => $sesi->title,
            'desc' => $sesi->description ?? '-',
            'kelas' => $sesi->category_badge ?? '-',
            'ruang' => $sesi->room_name ?? '-',
            'siswa' => $sesi->attendances()->count().' Siswa Terdaftar',
            'badge' => $tanggal->isToday() ? 'Akan Dimulai Segera' : null,
            'badgeTone' => 'amber',
            'actions' => [
                ['label' => 'Input Absen Kelas', 'icon' => 'clipboard-form', 'tone' => 'ember'],
                ['label' => 'Buka Modul Ajar', 'icon' => 'book-open', 'tone' => 'bark'],
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
