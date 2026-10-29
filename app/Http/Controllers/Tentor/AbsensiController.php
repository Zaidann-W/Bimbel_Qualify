<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        if (! $staffProfile) {
            return view('tentor.absensi', [
                'sessions' => collect(),
                'students' => collect(),
                'attendances' => collect(),
                'session' => null,
            ]);
        }

        $today = today();

        // Ambil sesi hari ini untuk tentor ini
        $sessions = ClassSession::with(['classGroup', 'subject', 'room', 'studyShift'])
            ->where('staff_id', $staffProfile->id)
            ->whereDate('session_date', $today)
            ->orderBy('start_time')
            ->get();

        // Ambil sesi pertama (atau yang dipilih via query param)
        $selectedSessionId = $request->query('session_id');
        $session = $sessions->firstWhere('id', $selectedSessionId) ?? $sessions->first();

        $students = collect();
        $attendances = collect();

        if ($session) {
            // Ambil siswa yang terdaftar di class group ini
            $studentIds = Enrollment::where('class_group_id', $session->class_group_id)
                ->where('status', 'aktif')
                ->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'nis', 'nisn', 'school_name']);

            // Ambil absensi yang sudah ada untuk sesi ini
            $attendances = StudentAttendance::where('class_session_id', $session->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');
        }

        return view('tentor.absensi', [
            'sessions' => $sessions,
            'session' => $session,
            'students' => $students,
            'attendances' => $attendances,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        if (! $staffProfile) {
            return back()->withErrors(['error' => 'Profil tentor tidak ditemukan.']);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:class_sessions,id',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'attendances.*.arrived_at' => 'nullable|date_format:H:i',
            'attendances.*.note' => 'nullable|string',
        ]);

        $session = ClassSession::findOrFail($validated['session_id']);

        // Verify this session belongs to this tentor
        if ($session->staff_id !== $staffProfile->id) {
            return back()->withErrors(['error' => 'Anda tidak berhak menginput absensi untuk sesi ini.']);
        }

        foreach ($validated['attendances'] as $attendanceData) {
            $arrivedAt = null;
            if (! empty($attendanceData['arrived_at'])) {
                $arrivedAt = $session->session_date->copy()->setTimeFromTimeString($attendanceData['arrived_at']);
            }

            StudentAttendance::updateOrCreate(
                [
                    'class_session_id' => $session->id,
                    'student_id' => $attendanceData['student_id'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'arrived_at' => $arrivedAt,
                    'note' => $attendanceData['note'] ?? null,
                    'recorded_by' => $user->id,
                ]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
