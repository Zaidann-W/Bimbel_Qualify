<?php

test('pendaftaran orang tua page returns a successful response', function () {
    $response = $this->get('/wali/pendaftaran');

    $response->assertStatus(200);
    $response->assertSee('Status Pendaftaran Calon Siswa');
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Audit &amp; Verifikasi Dokumen Persyaratan', false);
    $response->assertSee('Perlu Perbaikan Berkas');
    $response->assertSee('Unggah Perbaikan');
    $response->assertSee('3 Berkas Valid');
    $response->assertSee('Butuh Bantuan Panitia PSB?');
});

test('pendaftaran orang tua is reachable from its named route', function () {
    $this->get(route('wali.pendaftaran'))->assertStatus(200);
});
