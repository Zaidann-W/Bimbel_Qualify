<?php

test('informasi anak page returns a successful response', function () {
    $response = $this->get('/wali/informasi');

    $response->assertStatus(200);
    $response->assertSee('Informasi Akademik');
    $response->assertSee('Profil Sedang Ditampilkan');
    $response->assertSee('Program &amp; Rombongan Belajar', false);
    $response->assertSee('Jadwal Belajar Mingguan');
    $response->assertSee('Wali Kelas Akademik');
    $response->assertSee('Beralih ke Profil Anindya');
});

test('informasi anak is reachable from its named route', function () {
    $this->get(route('wali.informasi'))->assertStatus(200);
});
