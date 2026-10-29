<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Services\TentorQualifixService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TentorQualifixController extends Controller
{
    public function __construct(private readonly TentorQualifixService $service) {}

    /**
     * Tampilkan halaman Qualix (Klinik PR) tentor: antrean pertanyaan siswa dan ruang solusi.
     * Semua data diambil dari basis data (QualifixTicket yang ditugaskan kepada tentor ini).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('tentor.qualifix', $this->service->qualifix($user));
    }
}
