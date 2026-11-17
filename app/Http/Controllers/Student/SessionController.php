<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * Tampilkan jadwal belajar siswa: sesi hari ini, sesi mendatang, dan sesi lampau.
     */
    public function schedule(Request $request): View
    {
        $sessions = StudySession::query()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

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
            'contextLabel' => $this->academicContextLabel($request->user()->load('student.enrollments.classGroup.academicPeriod')),
        ]);
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
