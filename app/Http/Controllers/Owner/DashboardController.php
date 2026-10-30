<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\OwnerDashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly OwnerDashboardService $service) {}

    public function index()
    {
        return view('owner.dashboard', $this->service->dashboard());
    }

    public function laporanKeuangan()
    {
        $data = $this->service->dashboard()['laporanKeuangan'];

        return view('owner.laporan-keuangan', $data);
    }
}
