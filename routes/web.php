<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentor', function () {
    return view('tentor.dashboard');
})->name('tentor.dashboard');
