<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly StudentDashboardService $service) {}

    /**
     * Tampilkan ringkasan belajar siswa: modul, presensi, klinik soal, jadwal, dan pengumuman.
     */
    public function index(Request $request): View
    {
        $student = $request->user()->student;
        $classGroup = $student?->enrollments()->where('status', 'aktif')->first()?->classGroup;

        return view('siswa.dashboard', $this->service->dashboard($student, $classGroup) + [
            'student' => $student,
            'classGroup' => $classGroup,
        ]);
    }
}
