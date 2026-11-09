<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrasiRequest;
use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class RegistrasiController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create(): View
    {
        $gelombangAktif = AdmissionBatch::current();
        $gelombangBerikutnya = AdmissionBatch::next();
        $tahunMulai = AcademicPeriod::currentYearStart();

        return view('auth.registrasi', [
            'gelombangAktif' => $gelombangAktif,
            'gelombangBerikutnya' => $gelombangBerikutnya,
            'tahunAjaran' => $gelombangAktif?->academicPeriod?->academic_year
                ?? $gelombangBerikutnya?->academicPeriod?->academic_year
                ?? $tahunMulai.'/'.($tahunMulai + 1),
            'pesanPendaftaranTutup' => RegistrasiRequest::PESAN_PENDAFTARAN_TUTUP,
        ]);
    }

    /**
     * Handle the registration form submission.
     */
    public function store(RegistrasiRequest $request, RegistrationService $service): RedirectResponse
    {
        $result = $service->register($request);

        // Set flash session data
        Session::flash('registration_number', $result['registration_number']);
        Session::flash('application_id', $result['application_id']);

        // Redirect to konfirmasi page
        return redirect()->route('registrasi.konfirmasi');
    }

    /**
     * Show the registration confirmation page.
     */
    public function konfirmasi(): View|RedirectResponse
    {
        $registrationNumber = Session::get('registration_number');

        if (! $registrationNumber) {
            return redirect()->route('registrasi');
        }

        return view('auth.registrasi-konfirmasi', [
            'registration_number' => $registrationNumber,
        ]);
    }
}
