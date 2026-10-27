<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DataSiswaController extends Controller
{
    /**
     * Tampilkan database induk siswa dan informasi kelas.
     */
    public function index(): View
    {
        return view('admin.datasiswa');
    }
}
