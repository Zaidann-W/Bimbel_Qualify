<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/registrasi', function () {
    return view('registrasi');
})->name('registrasi');

Route::post('/registrasi', function () {
    return view('registrasi');
})->name('registrasi.simpan');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function () {
    return view('login');
})->name('login.simpan');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/tentor', function () {
    return view('tentor.dashboard');
})->name('tentor.dashboard');

Route::get('/tentor/absensi', function () {
    return view('tentor.absensi');
})->name('tentor.absensi');

Route::get('/tentor/qualifix', function () {
    return view('tentor.qualifix');
})->name('tentor.qualifix');

Route::get('/tentor/materi', function () {
    return view('tentor.materi');
})->name('tentor.materi');

Route::get('/tentor/profil', function () {
    return view('tentor.profil');
})->name('tentor.profil');

Route::get('/siswa', function () {
    return view('siswa.dashboard');
})->name('siswa.dashboard');

Route::get('/owner', function () {
    return view('owner.dashboard');
})->name('owner.dashboard');

Route::get('/owner/laporan-keuangan', function () {
    return view('owner.laporan-keuangan');
})->name('owner.laporanKeuangan');

Route::get('/owner/laporan-absensi', function () {
    return view('owner.laporan-absensi');
})->name('owner.laporanAbsensi');

Route::get('/owner/pemantauan-materi', function () {
    return view('owner.pemantauan-materi');
})->name('owner.pemantauanMateri');

Route::get('/owner/pengaturan-website', function () {
    return view('owner.pengaturan-website');
})->name('owner.pengaturanWebsite');

Route::get('/manager', function () {
    return view('manager.dashboard');
})->name('manager.dashboard');

Route::get('/manager/keuangan-bulanan', function () {
    return view('manager.keuangan-bulanan');
})->name('manager.keuanganBulanan');

Route::get('/manager/pemantauan-materi', function () {
    return view('manager.pemantauan-materi');
})->name('manager.pemantauanMateri');

Route::get('/manager/profil', function () {
    return view('manager.profil');
})->name('manager.profil');

Route::post('/manager/profil', function () {
    return view('manager.profil');
})->name('manager.profilSimpan');

Route::get('/siswa/jadwal/detail', function () {
    return view('siswa.detail-pertemuan');
})->name('siswa.jadwal.detail');

Route::get('/siswa/jadwal', function () {
    return view('siswa.detail-jadwal');
})->name('siswa.jadwal');

Route::get('/siswa/materi', function () {
    return view('siswa.materi');
})->name('siswa.materi');

Route::get('/siswa/qualifix', function () {
    return view('siswa.qualifix');
})->name('siswa.qualifix');

Route::get('/siswa/profil', function () {
    return view('siswa.profil');
})->name('siswa.profil');

Route::get('/wali', function () {
    return view('wali.dashboard');
})->name('wali.dashboard');

Route::get('/wali/kehadiran', function () {
    return view('wali.kehadiran');
})->name('wali.kehadiran');

Route::get('/wali/informasi', function () {
    return view('wali.informasi');
})->name('wali.informasi');

Route::get('/wali/pembayaran', function () {
    return view('wali.pembayaran');
})->name('wali.pembayaran');

Route::get('/wali/pendaftaran', function () {
    return view('wali.pendaftaran');
})->name('wali.pendaftaran');

Route::get('/wali/profil', function () {
    return view('wali.profil');
})->name('wali.profil');
