<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Services\PembayaranService;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(private readonly PembayaranService $service) {}

    public function index(?string $periode = null): View
    {
        return view('wali.pembayaran', $this->service->pembayaran($periode));
    }
}
