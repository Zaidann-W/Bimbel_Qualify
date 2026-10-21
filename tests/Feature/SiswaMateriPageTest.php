<?php

test('materi pembelajaran siswa page returns a successful response', function () {
    $response = $this->get('/siswa/materi');

    $response->assertStatus(200);
    $response->assertSee('Katalog &amp; Bank Modul Belajar Digital', false);
    $response->assertSee('Modul Bank Soal HOTS UTBK 2025');
    $response->assertSee('Baca Modul');
    $response->assertSee('Perlindungan Hak Cipta &amp; Hak Use', false);
});

test('materi pembelajaran siswa is reachable from its named route', function () {
    $this->get(route('siswa.materi'))->assertStatus(200);
});
