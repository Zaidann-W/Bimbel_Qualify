<?php

use App\Http\Controllers\Admin\AbsensiJadwalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataSiswaController;
use App\Http\Controllers\Admin\KelasController;
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
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanAbsensiController;
use App\Http\Controllers\Owner\PemantauanMateriController;
use App\Http\Controllers\Owner\PengaturanWebsiteController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\SessionController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Tentor\AbsensiController;
use App\Http\Controllers\Tentor\ProfilController;
use App\Http\Controllers\Tentor\TentorDashboardController;
use App\Http\Controllers\Tentor\TentorQualifixController;
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
    Route::get('/data-siswa/create', [DataSiswaController::class, 'create'])->name('dataSiswa.create');
    Route::post('/data-siswa', [DataSiswaController::class, 'store'])->name('dataSiswa.store');
    Route::get('/data-siswa/{student}', [DataSiswaController::class, 'show'])->name('dataSiswa.show');
    Route::get('/data-siswa/{student}/edit', [DataSiswaController::class, 'edit'])->name('dataSiswa.edit');
    Route::put('/data-siswa/{student}', [DataSiswaController::class, 'update'])->name('dataSiswa.update');
    Route::delete('/data-siswa/{student}', [DataSiswaController::class, 'destroy'])->name('dataSiswa.destroy');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{classGroup}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{classGroup}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{classGroup}', [KelasController::class, 'destroy'])->name('kelas.destroy');

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
        Route::get('/', [TentorDashboardController::class, 'index'])->name('dashboard');

        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::get('/qualifix', [TentorQualifixController::class, 'index'])->name('qualifix');

        Route::get('/materi', function () {
            return view('tentor.materi');
        })->name('materi');

        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
    });

    // Siswa routes
    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');

        Route::get('/jadwal/detail', [SessionController::class, 'schedule'])->name('jadwal.detail');

        Route::get('/jadwal/{studySession}', [SessionController::class, 'show'])
            ->whereNumber('studySession')
            ->name('jadwal.show');

        Route::get('/jadwal', [SessionController::class, 'current'])->name('jadwal');

        Route::get('/materi', [StudentController::class, 'materi'])->name('materi');

        Route::get('/qualifix', [StudentController::class, 'qualifix'])->name('qualifix');

        Route::get('/profil', [StudentController::class, 'profil'])->name('profil');
    });

    // Owner routes
    Route::prefix('owner')->name('owner.')->middleware('role:owner')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/laporan-keuangan', [OwnerDashboardController::class, 'laporanKeuangan'])->name('laporanKeuangan');

        Route::get('/laporan-absensi', [LaporanAbsensiController::class, 'index'])->name('laporanAbsensi');

        Route::get('/pemantauan-materi', [PemantauanMateriController::class, 'index'])->name('pemantauanMateri');

        Route::get('/pengaturan-website', [PengaturanWebsiteController::class, 'index'])->name('pengaturanWebsite');

        Route::post('/pengaturan-website', [PengaturanWebsiteController::class, 'update'])->name('pengaturanWebsite.update');
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
