<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class SessionService
{
    /**
     * Kunci session yang menyimpan timestamp aktivitas terakhir pengguna.
     */
    public const ACTIVITY_KEY = 'auth_last_activity_at';

    /**
     * Dapatkan nama cookie penanda aktivitas.
     */
    public function activityCookieName(): string
    {
        return (string) config('session.activity_cookie');
    }

    /**
     * Batas waktu diam dalam detik. 0 berarti tanpa batas waktu diam.
     */
    public function idleTimeoutInSeconds(): int
    {
        return max(0, (int) config('session.idle_timeout')) * 60;
    }

    /**
     * Tentukan apakah pengguna sudah melewati batas waktu diam.
     */
    public function hasExceededIdleTimeout(Request $request): bool
    {
        $timeout = $this->idleTimeoutInSeconds();

        if ($timeout === 0) {
            return false;
        }

        $lastActivityAt = $this->lastActivityAt($request);

        if ($lastActivityAt === null) {
            return false;
        }

        return (now()->timestamp - $lastActivityAt) > $timeout;
    }

    /**
     * Waktu aktivitas terakhir pengguna (detik Unix), null bila belum tercatat.
     *
     * Nilai session diprioritaskan karena tidak dapat diubah pengguna,
     * cookie dipakai sebagai cadangan ketika session sudah hilang.
     */
    public function lastActivityAt(Request $request): ?int
    {
        $value = $request->session()->get(self::ACTIVITY_KEY)
            ?? $request->cookies->get($this->activityCookieName());

        if ($value === null || ! is_numeric($value) || (int) $value <= 0) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Catat waktu aktivitas terakhir pengguna pada session dan cookie penanda.
     */
    public function markActive(Request $request): void
    {
        $timestamp = now()->timestamp;

        $request->session()->put(self::ACTIVITY_KEY, $timestamp);

        Cookie::queue(Cookie::forever($this->activityCookieName(), (string) $timestamp));
    }

    /**
     * Keluarkan pengguna dari sistem dan hapus seluruh isi session,
     * sehingga pengguna wajib login ulang dengan kredensialnya.
     */
    public function terminate(Request $request): void
    {
        // Mengeluarkan user dan meng-cycle token "remember me", sehingga
        // cookie login otomatis ikut mati di semua perangkat.
        Auth::logout();

        // Membuang SEMUA isi session (data login, flash, role, penanda
        // aktivitas, dll.) sehingga tidak ada sisa data yang nyangkut.
        $request->session()->flush();

        // Menghapus seluruh isi session lama di storage lalu memulai session
        // baru dengan ID yang berbeda.
        $request->session()->invalidate();

        // Membuat ulang token CSRF agar token sebelumnya tidak dapat dipakai lagi.
        $request->session()->regenerateToken();

        // Menghapus cookie penanda aktivitas agar timer diam dimulai dari nol
        // saat pengguna login kembali.
        Cookie::queue(Cookie::forget($this->activityCookieName()));
    }
}
