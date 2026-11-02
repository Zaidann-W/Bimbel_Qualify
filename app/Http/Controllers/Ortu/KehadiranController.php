<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Services\KehadiranService;
use Illuminate\View\View;

class KehadiranController extends Controller
{
    public function __construct(private readonly KehadiranService $service) {}

    public function index(): View
    {
        return view('wali.kehadiran', $this->service->kehadiran());
    }
}
