<?php

use App\Http\Controllers\Admin\AbsensiJadwalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataSiswaController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\LaporanPengaturanController;
use App\Http\Controllers\Admin\ManajemenMateriController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\QualifixController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegistrasiController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Student\SessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest only)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran');
    Route::get('/pendaftaran/{kode}', [PendaftaranController::class, 'show'])->name('pendaftaran.detail');

    Route::get('/data-siswa', [DataSiswaController::class, 'index'])->name('dataSiswa');

    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan');

    Route::get('/manajemen-materi', [ManajemenMateriController::class, 'index'])->name('manajemenMateri');

    Route::get('/absensi-jadwal', [AbsensiJadwalController::class, 'index'])->name('absensiJadwal');

    Route::get('/qualifix', [QualifixController::class, 'index'])->name('qualifix');

    Route::get('/laporan-pengaturan', [LaporanPengaturanController::class, 'index'])->name('laporanPengaturan');
});

Route::get('/registrasi', [RegistrasiController::class, 'create'])
    ->name('registrasi');

Route::post('/registrasi', [RegistrasiController::class, 'store'])
    ->name('registrasi.simpan');

Route::get('/registrasi/konfirmasi', [RegistrasiController::class, 'konfirmasi'])
    ->name('registrasi.konfirmasi');

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

        Route::get('/jadwal/{studySession}', [SessionController::class, 'show'])
            ->whereNumber('studySession')
            ->name('jadwal.show');

        Route::get('/jadwal', [SessionController::class, 'current'])->name('jadwal');

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
