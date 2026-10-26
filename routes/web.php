<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest only)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/registrasi', function () {
    return view('registrasi');
})->name('registrasi');

Route::post('/registrasi', function () {
    return view('registrasi');
})->name('registrasi.simpan');

// Login routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.simpan');

    // Password reset routes
    Route::get('/lupa-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/pendaftaran', function () {
            return view('admin.pendaftaran');
        })->name('pendaftaran');

        Route::get('/pendaftaran/{kode}', function (string $kode) {
            return view('admin.pendaftaran-detail', ['kode' => $kode]);
        })->name('pendaftaran.detail');

        Route::get('/data-siswa', function () {
            return view('admin.datasiswa');
        })->name('dataSiswa');

        Route::get('/keuangan', function () {
            return view('admin.keuangan');
        })->name('keuangan');

        Route::get('/manajemen-materi', function () {
            return view('admin.manajemen-materi');
        })->name('manajemenMateri');

        Route::get('/absensi-jadwal', function () {
            return view('admin.absensi-jadwal');
        })->name('absensiJadwal');

        Route::get('/qualifix', function () {
            return view('admin.qualifix');
        })->name('qualifix');

        Route::get('/laporan-pengaturan', function () {
            return view('admin.laporan-pengaturan');
        })->name('laporanPengaturan');
    });

    // Tentor routes
    Route::prefix('tentor')->name('tentor.')->middleware('role:tentor')->group(function () {
        Route::get('/', function () {
            return view('tentor.dashboard');
        })->name('dashboard');

        Route::get('/absensi', function () {
            return view('tentor.absensi');
        })->name('absensi');

        Route::get('/qualifix', function () {
            return view('tentor.qualifix');
        })->name('qualifix');

        Route::get('/materi', function () {
            return view('tentor.materi');
        })->name('materi');

        Route::get('/profil', function () {
            return view('tentor.profil');
        })->name('profil');
    });

    // Siswa routes
    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('/', function () {
            return view('siswa.dashboard');
        })->name('dashboard');

        Route::get('/jadwal/detail', function () {
            return view('siswa.detail-pertemuan');
        })->name('jadwal.detail');

        Route::get('/jadwal', function () {
            return view('siswa.detail-jadwal');
        })->name('jadwal');

        Route::get('/materi', function () {
            return view('siswa.materi');
        })->name('materi');

        Route::get('/qualifix', function () {
            return view('siswa.qualifix');
        })->name('qualifix');

        Route::get('/profil', function () {
            return view('siswa.profil');
        })->name('profil');
    });

    // Owner routes
    Route::prefix('owner')->name('owner.')->middleware('role:owner')->group(function () {
        Route::get('/', function () {
            return view('owner.dashboard');
        })->name('dashboard');

        Route::get('/laporan-keuangan', function () {
            return view('owner.laporan-keuangan');
        })->name('laporanKeuangan');

        Route::get('/laporan-absensi', function () {
            return view('owner.laporan-absensi');
        })->name('laporanAbsensi');

        Route::get('/pemantauan-materi', function () {
            return view('owner.pemantauan-materi');
        })->name('pemantauanMateri');

        Route::get('/pengaturan-website', function () {
            return view('owner.pengaturan-website');
        })->name('pengaturanWebsite');
    });

    // Manager routes
    Route::prefix('manager')->name('manager.')->middleware('role:manager')->group(function () {
        Route::get('/', function () {
            return view('manager.dashboard');
        })->name('dashboard');

        Route::get('/keuangan-bulanan', function () {
            return view('manager.keuangan-bulanan');
        })->name('keuanganBulanan');

        Route::get('/pemantauan-materi', function () {
            return view('manager.pemantauan-materi');
        })->name('pemantauanMateri');

        Route::get('/profil', function () {
            return view('manager.profil');
        })->name('profil');

        Route::post('/profil', function () {
            return view('manager.profil');
        })->name('profilSimpan');
    });

    // Ortu (Wali) routes
    Route::prefix('ortu')->name('ortu.')->middleware('role:ortu')->group(function () {
        Route::get('/', function () {
            return view('wali.dashboard');
        })->name('dashboard');

        Route::get('/kehadiran', function () {
            return view('wali.kehadiran');
        })->name('kehadiran');

        Route::get('/informasi', function () {
            return view('wali.informasi');
        })->name('informasi');

        Route::get('/pembayaran', function () {
            return view('wali.pembayaran');
        })->name('pembayaran');

        Route::get('/pendaftaran', function () {
            return view('wali.pendaftaran');
        })->name('pendaftaran');

        Route::get('/profil', function () {
            return view('wali.profil');
        })->name('profil');
    });
});
