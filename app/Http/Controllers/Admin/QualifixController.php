<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class QualifixController extends Controller
{
    /**
     * Tampilkan tiket Klinik PR (QualiFix) yang masuk.
     */
    public function index(): View
    {
        return view('admin.qualifix');
    }
}
