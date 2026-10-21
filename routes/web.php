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

Route::get('/owner/laporan-keuangan', function () {
    return view('owner.laporan-keuangan');
})->name('owner.laporanKeuangan');
