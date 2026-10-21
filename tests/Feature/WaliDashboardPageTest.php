<?php

test('dashboard orang tua returns a successful response', function () {
    $response = $this->get('/wali');

    $response->assertStatus(200);
    $response->assertSee('Selamat datang kembali, Bapak Ir. Hendra Gunawan');
    $response->assertSee('Presensi Semester Berjalan');
    $response->assertSee('Jadwal Belajar Pekan Ini');
    $response->assertSee('Agenda Konsultasi Triwulan');
    $response->assertSee('Portal Monitoring Orang Tua');
});

test('dashboard orang tua is reachable from its named route', function () {
    $this->get(route('wali.dashboard'))->assertStatus(200);
});
