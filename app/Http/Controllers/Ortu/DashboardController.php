<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Services\OrtuDashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly OrtuDashboardService $service) {}

    public function index()
    {
        return view('wali.dashboard', $this->service->dashboard());
    }
}
