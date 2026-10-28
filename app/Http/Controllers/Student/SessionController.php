<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
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

    /**
     * Tampilkan daftar jadwal belajar siswa beserta statistik pekan berjalan.
     */
    public function jadwal(Request $request): View
    {
        $sessions = StudySession::query()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $runningToday = $sessions->filter(fn (StudySession $session): bool => $session->date->isToday());
        $thisWeek = $sessions->filter(fn (StudySession $session): bool => $session->date->between($weekStart, $weekEnd));

        $filter = $sessions->pluck('category_badge')->filter()->unique()->prepend('Semua Matpel')->values();

        $mainRoom = $sessions->first(fn (StudySession $session): bool => $session->room_name !== null);

        $student = $request->user()->student;
        $classGroup = $student?->enrollments()->where('status', 'aktif')->first()?->classGroup;
        $periode = $classGroup?->academicPeriod;

        $context = collect([$periode?->semester, $periode?->academic_year])
            ->filter()
            ->implode(' ')
            ?: 'Semester Berjalan';

        $context .= $classGroup !== null
            ? ' • Kelas '.$classGroup->grade.' '.$classGroup->major
            : '';

        return view('siswa.detail-pertemuan', [
            'sessions' => $sessions,
            'runningToday' => $runningToday,
            'thisWeek' => $thisWeek,
            'filter' => $filter,
            'mainRoom' => $mainRoom,
            'context' => $context,
        ]);
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
}
