<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ManajemenMateriController extends Controller
{
    /**
     * Tampilkan pusat materi ajar dan berkas pembelajaran.
     */
    public function index(): View
    {
        return view('admin.manajemen-materi');
    }
}
