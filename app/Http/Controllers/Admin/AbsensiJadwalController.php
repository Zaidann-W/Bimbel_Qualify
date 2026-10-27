<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AbsensiJadwalController extends Controller
{
    /**
     * Tampilkan jadwal sesi, presensi siswa/tentor, dan pengajuan izin.
     */
    public function index(): View
    {
        return view('admin.absensi-jadwal');
    }
}
