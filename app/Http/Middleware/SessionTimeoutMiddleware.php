<?php

namespace App\Http\Middleware;

use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    public function __construct(private readonly SessionService $sessionService) {}

    /**
     * Paksa pengguna keluar ketika tidak aktif melewati batas waktu,
     * berlaku untuk seluruh role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Selesaikan status login lebih dulu (termasuk cookie "ingat saya")
        // agar cookie login otomatis pun ikut diperiksa batas waktunya.
        $user = $request->user();

        if ($user !== null) {
            if ($this->sessionService->hasExceededIdleTimeout($request)) {
                $this->sessionService->terminate($request);

                return redirect()->route('login')->with('status', sprintf(
                    'Sesi Anda berakhir setelah %d menit tidak aktif. Silakan masuk kembali.',
                    (int) config('session.idle_timeout'),
                ));
            }

            $this->sessionService->markActive($request);
        }

        return $next($request);
    }
}
