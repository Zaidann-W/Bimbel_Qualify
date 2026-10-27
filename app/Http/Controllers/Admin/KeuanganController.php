<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    /**
     * Tampilkan pusat keuangan, tagihan SPP, dan transaksi.
     */
    public function index(): View
    {
        return view('admin.keuangan');
    }
}
