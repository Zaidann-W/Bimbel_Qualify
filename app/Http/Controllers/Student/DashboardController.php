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

        // Ambil SEMUA kelas aktif yang siswa pendaftar (bukan hanya kelas pertama)
        // Gunakan ->all() agar tetap berupa array of OBJECTS (bukan array of arrays)
        $activeEnrollments = $student?->enrollments()->where('status', 'aktif')->get() ?? collect();
        $classGroups = $activeEnrollments->pluck('classGroup')->filter()->unique()->values()->all();

        return view('siswa.dashboard', $this->service->dashboard($student, $classGroups) + [
            'student' => $student,
            'classGroups' => $classGroups,
        ]);
    }
}
