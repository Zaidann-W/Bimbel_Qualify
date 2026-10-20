<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentor', function () {
    return view('tentor.dashboard');
})->name('tentor.dashboard');

Route::get('/tentor/absensi', function () {
    return view('tentor.absensi');
})->name('tentor.absensi');
