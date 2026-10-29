<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Services\TentorDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TentorDashboardController extends Controller
{
    public function __construct(private readonly TentorDashboardService $service) {}

    /**
     * Tampilkan ringkasan mengajar tentor: kelas hari ini, absensi, klinik PR, dan jadwal.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('tentor.dashboard', $this->service->dashboard($user));
    }
}
