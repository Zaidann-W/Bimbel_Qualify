<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(Request $request)
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if ($request->user()) {
            return redirect()->intended($request->user()->getDashboardRoute());
        }

        return view('login');
    }

    /**
     * Proses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->validated('email');
        $password = $request->validated('password');
        $remember = $request->boolean('remember');

        // Rate limiting: 5 percobaan per menit per email + IP
        $limiterKey = 'login:'.$email.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            $seconds = RateLimiter::availableIn($limiterKey);

            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam '.$seconds.' detik.',
            ]);
        }

        // Cek kredensial - gunakan Auth::attempt yang sudah handle hash
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($limiterKey, 60); // 1 menit decay

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        // Get authenticated user (includes soft deleted check via provider)
        $user = Auth::user();

        // Cek status user (nonaktif atau soft deleted)
        if ($user->status === 'nonaktif' || $user->trashed()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun ini dinonaktifkan. Silakan hubungi admin.',
            ]);
        }

        // Login berhasil - reset rate limiter
        RateLimiter::clear($limiterKey);

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Update last_login_at
        $user->update(['last_login_at' => now()]);

        // Redirect ke dashboard sesuai role
        return redirect()->intended($user->getDashboardRoute());
    }
}
