<?php

test('detail jadwal siswa page returns a successful response', function () {
    $response = $this->get('/siswa/jadwal');

    $response->assertStatus(200);
    $response->assertSee('Kalkulus &amp; Aplikasi Integral: Luas &amp; Volume Benda Putar', false);
    $response->assertSee('Materi &amp; Bahan Ajar Sesi', false);
    $response->assertSee('Status Presensi &amp; Izin', false);
    $response->assertSee('Ajukan Formulir Izin');
});

test('detail jadwal siswa renders the izin modal', function () {
    $response = $this->get(route('siswa.jadwal'));

    $response->assertStatus(200);
    $response->assertSee('izin-modal');
    $response->assertSee('Pengajuan Izin / Berhalangan Hadir');
    $response->assertSee('Kirim Formulir Izin');
});
