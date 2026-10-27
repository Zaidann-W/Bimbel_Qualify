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
