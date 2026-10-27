<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Logout user: hapus seluruh session sehingga wajib login ulang.
     */
    public function destroy(Request $request, SessionService $sessions): RedirectResponse
    {
        $sessions->terminate($request);

        return redirect()->route('login')->with('status', 'Anda telah keluar dari akun. Silakan masuk kembali untuk melanjutkan.');
    }
}
