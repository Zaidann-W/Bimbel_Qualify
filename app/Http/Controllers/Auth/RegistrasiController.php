<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrasiRequest;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class RegistrasiController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.registrasi');
    }

    /**
     * Handle the registration form submission.
     *
     * @param  \App\Http\Requests\RegistrasiRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RegistrasiRequest $request, RegistrationService $service): RedirectResponse
    {
        $result = $service->register($request);

        // Set flash session data
        Session::flash('registration_number', $result['registration_number']);
        Session::flash('application_id', $result['application_id']);

        // Redirect to konfirmasi page
        return redirect()->route('registrasi.konfirmasi')->withFlash();
    }

    /**
     * Show the registration confirmation page.
     *
     * @return View
     */
    public function konfirmasi(): View
    {
        $registrationNumber = Session::get('registration_number');

        return view('auth.registrasi-konfirmasi', [
            'registration_number' => $registrationNumber,
        ]);
    }
}