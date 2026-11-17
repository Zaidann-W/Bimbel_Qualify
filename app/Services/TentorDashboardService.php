<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
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

        $jobTitle = $staffProfile?->job_title ?? $user?->name ?? 'Tentor';
        $today = today();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Sesi mengajar hari ini untuk tentor ini
        $sessionsToday = $this->sesiMengajar($staffProfile, $today, $today);

        // Sesi minggu ini untuk tentor ini
        $sessionsThisWeek = $this->sesiMengajar($staffProfile, $weekStart, $weekEnd);

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
        $totalMinutes = $sessionsThisWeek->sum(fn (ClassSession $sesi): int => $this->durasiMenit($sesi));
        $totalTeachingHours = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0.0;

        return [
            'user' => $user,
            'staffProfile' => $staffProfile,
            'jobTitle' => $jobTitle,
            'stats' => $this->statCards($totalSessionsToday, $attendancePending, $qualifixTickets->count(), $totalTeachingHours),
            'sessionsToday' => $sessionsToday->map(fn (ClassSession $sesi) => $this->kartuSesi($sesi)),
            'qualifixTickets' => $qualifixTickets->map(fn (QualifixTicket $ticket) => $this->kartuKlinikPR($ticket)),
            'totalTeachingHours' => $totalTeachingHours,
            'todayDate' => $today->locale('id')->translatedFormat('j F Y'),
            'todayTime' => now()->format('H:i'),
        ];
    }

    /**
     * Sesi mengajar milik satu tentor pada rentang tanggal tertentu.
     *
     * Sesi dibaca dari tabel class_sessions (sistem jadwal terbaru) dan
     * dicocokkan lewat staff_id, bukan lagi lewat teks nama tentor.
     *
     * @return Collection<int, ClassSession>
     */
    private function sesiMengajar(?StaffProfile $staffProfile, Carbon $dari, Carbon $sampai): Collection
    {
        if ($staffProfile === null) {
            return collect();
        }

        return ClassSession::query()
            ->with([
                'classGroup' => fn ($q) => $q->withCount([
                    'enrollments' => fn ($e) => $e->where('status', 'aktif'),
                ]),
                'subject',
                'room',
            ])
            ->withCount('attendance')
            ->where('staff_id', $staffProfile->id)
            ->where('status', '!=', 'dibatalkan')
            ->whereBetween('session_date', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Hitung berapa sesi yang belum diinput absensinya sama sekali.
     *
     * @param  Collection<int, ClassSession>  $sessions
     */
    private function countAttendancePending(Collection $sessions): int
    {
        return $sessions
            ->filter(fn (ClassSession $sesi): bool => $sesi->attendance_count === 0)
            ->count();
    }

    /**
     * Durasi satu sesi dalam menit, dihitung dari jam mulai dan selesai.
     */
    private function durasiMenit(ClassSession $sesi): int
    {
        $mulai = Carbon::parse($sesi->start_time);
        $selesai = Carbon::parse($sesi->end_time);

        if ($selesai->lessThan($mulai)) {
            $selesai->addDay();
        }

        return (int) $mulai->diffInMinutes($selesai);
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
    private function kartuSesi(ClassSession $sesi): array
    {
        $tanggal = $sesi->tanggal;

        return [
            'waktu' => sprintf('%s – %s WIB', $this->jam($sesi->start_time), $this->jam($sesi->end_time)),
            'mapel' => $sesi->subject?->name ?? $sesi->topic ?? 'Mata Pelajaran',
            'desc' => $sesi->topic ?? '-',
            'kelas' => $sesi->classGroup?->code ?? '-',
            'ruang' => $this->ruang($sesi),
            'siswa' => ($sesi->classGroup?->enrollments_count ?? 0).' Siswa Terdaftar',
            'badge' => $tanggal->isToday() ? 'Akan Dimulai Segera' : null,
            'badgeTone' => 'amber',
            'actions' => [
                [
                    'label' => 'Input Absen Kelas',
                    'icon' => 'clipboard-form',
                    'tone' => 'ember',
                    'href' => route('tentor.absensi', ['session_id' => $sesi->id]),
                ],
                [
                    'label' => 'Buka Modul Ajar',
                    'icon' => 'book-open',
                    'tone' => 'bark',
                    'href' => route('tentor.materi', [
                        'subject_id' => $sesi->subject_id,
                        'class_group_id' => $sesi->class_group_id,
                    ]),
                ],
            ],
        ];
    }

    /**
     * Nama ruang beserta lantai, dengan nilai cadangan bila belum ditentukan.
     */
    private function ruang(ClassSession $sesi): string
    {
        $nama = trim(sprintf('%s %s', $sesi->room?->name ?? '', $sesi->room?->floor ?? ''));

        return $nama === '' ? 'Ruang belum ditentukan' : $nama;
    }

    /**
     * Jam dalam format Indonesia, misalnya "15.30".
     */
    private function jam(?string $waktu): string
    {
        return $waktu === null ? '-' : str_replace(':', '.', substr($waktu, 0, 5));
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
