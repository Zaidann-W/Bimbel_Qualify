<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\StudySession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * Tampilkan jadwal belajar siswa: sesi hari ini, sesi mendatang, dan sesi lampau.
     *
     * Menampilkan dua sumber jadwal sekaligus: sesi belajar (study_sessions)
     * dan jadwal kelas (class_sessions) dari seluruh kelas aktif siswa.
     */
    public function schedule(Request $request): View
    {
        $sessions = StudySession::query()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $classSessions = $this->classSessionsForStudent($request);

        return view('siswa.detail-pertemuan', [
            'todaySessions' => $sessions
                ->filter(fn (StudySession $session): bool => $session->date->isToday())
                ->values(),
            // Dibandingkan dengan awal hari ini, bukan now(), agar sesi hari ini
            // tidak ikut terhitung sebagai sesi lampau karena lewat tengah malam.
            'upcomingSessions' => $sessions
                ->filter(fn (StudySession $session): bool => $session->date->greaterThan(today()))
                ->values(),
            'pastSessions' => $sessions
                ->filter(fn (StudySession $session): bool => $session->date->lessThan(today()))
                ->reverse()
                ->values(),
            'todayClassSessions' => $classSessions
                ->filter(fn (ClassSession $session): bool => Carbon::parse($session->session_date)->isToday())
                ->values(),
            'upcomingClassSessions' => $classSessions
                ->filter(fn (ClassSession $session): bool => Carbon::parse($session->session_date)->greaterThan(today()))
                ->values(),
            'pastClassSessions' => $classSessions
                ->filter(fn (ClassSession $session): bool => Carbon::parse($session->session_date)->lessThan(today()))
                ->reverse()
                ->values(),
            'contextLabel' => $this->academicContextLabel($request->user()->load('student.enrollments.classGroup.academicPeriod')),
        ]);
    }

    /**
     * Ambil seluruh jadwal kelas (class_sessions) dari kelas aktif siswa yang login.
     *
     * Hanya sesi yang tidak dibatalkan yang ikut ditampilkan.
     *
     * @return Collection<int, ClassSession>
     */
    private function classSessionsForStudent(Request $request): Collection
    {
        $student = $request->user()->student;

        if ($student === null) {
            return collect();
        }

        $classGroupIds = $student->enrollments()
            ->where('status', 'aktif')
            ->pluck('class_group_id');

        if ($classGroupIds->isEmpty()) {
            return collect();
        }

        return ClassSession::query()
            ->with(['subject', 'staff', 'room'])
            ->whereIn('class_group_id', $classGroupIds)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Tampilkan detail sesi belajar yang sedang berlangsung atau terdekat.
     */
    public function current(Request $request): View
    {
        $studySession = StudySession::query()
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->first()
            ?? StudySession::query()
                ->whereDate('date', '>', today())
                ->orderBy('date')
                ->orderBy('start_time')
                ->first()
            ?? StudySession::query()
                ->orderByDesc('date')
                ->orderByDesc('start_time')
                ->first();

        abort_if($studySession === null, 404, 'Belum ada sesi belajar yang tersedia.');

        return $this->renderDetail($studySession, $request);
    }

    /**
     * Tampilkan detail satu sesi belajar beserta materi dan presensi siswa.
     */
    public function show(Request $request, StudySession $studySession): View
    {
        return $this->renderDetail($studySession, $request);
    }

    private function renderDetail(StudySession $studySession, Request $request): View
    {
        $studySession->load([
            'materials' => fn ($query) => $query->orderBy('id'),
        ]);

        $student = $request->user()->student;

        $attendance = $student !== null
            ? $studySession->attendances()->whereBelongsTo($student)->first()
            : null;

        return view('siswa.detail-jadwal', [
            'studySession' => $studySession,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Label konteks akademik untuk header halaman, misalnya
     * "Semester Genap 2024/2025 • SMA Kelas 12 IPA".
     *
     * Bagian yang tidak tersedia (misal siswa belum punya rombel) dihilangkan,
     * dan label kosong dikembalikan bila tidak ada satu pun konteks yang diketahui.
     */
    private function academicContextLabel(User $user): string
    {
        $student = $user->student;
        $classGroup = $student?->enrollments->firstWhere('status', 'aktif')?->classGroup;
        $period = $classGroup?->academicPeriod;

        $semester = $period?->semester;
        $grade = (string) ($classGroup?->grade ?? '');
        $kelas = $classGroup === null || $grade === ''
            ? null
            : trim(sprintf(
                '%s %s %s',
                stripos($grade, 'kelas') !== false ? '' : 'Kelas',
                $grade,
                $classGroup->major,
            ));

        return collect([
            $semester !== null ? trim(sprintf('Semester %s %s', ucfirst($semester), $period->academic_year)) : null,
            $kelas,
        ])->filter()->implode(' • ');
    }
}
