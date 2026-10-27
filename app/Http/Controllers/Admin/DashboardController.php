<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = [
            'service' => $this->service,
            'recentActivities' => $this->service->recentActivities(),
            'activeUserCounts' => $this->service->activeUserCounts(),
        ];

        return view('admin.dashboard', $data);
    }
}
