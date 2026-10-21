<?php

test('profil siswa page returns a successful response', function () {
    $response = $this->get('/siswa/profil');

    $response->assertStatus(200);
    $response->assertSee('Profil Siswa');
    $response->assertSee('Data Diri Siswa');
    $response->assertSee('Kontak Orang Tua / Wali');
    $response->assertSee('Akun Pembelajaran');
    $response->assertSee('Simpan Perubahan');
    $response->assertSee('Data Terverifikasi Sekolah');
});

test('profil siswa is reachable from its named route', function () {
    $this->get(route('siswa.profil'))->assertStatus(200);
});
