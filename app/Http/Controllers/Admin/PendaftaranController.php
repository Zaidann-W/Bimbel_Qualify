<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Tampilkan daftar calon siswa yang sedang mendaftar.
     */
    public function index(): View
    {
        return view('admin.pendaftaran');
    }

    /**
     * Tampilkan detail satu calon siswa berdasarkan nomor pendaftaran.
     */
    public function show(string $kode): View
    {
        return view('admin.pendaftaran-detail', [
            'kode' => $kode,
        ]);
    }
}
