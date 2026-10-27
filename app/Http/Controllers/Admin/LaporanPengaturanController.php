<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LaporanPengaturanController extends Controller
{
    /**
     * Tampilkan pusat laporan ekspor dan pengaturan portal admin.
     */
    public function index(): View
    {
        return view('admin.laporan-pengaturan');
    }
}
