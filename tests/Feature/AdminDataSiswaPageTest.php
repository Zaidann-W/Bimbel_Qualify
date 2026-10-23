<?php

test('data siswa returns a successful response', function () {
    $response = $this->get('/admin/data-siswa');

    $response->assertStatus(200);
    $response->assertSee('Data Siswa');
    $response->assertSee('Database induk siswa terdaftar dan informasi kontak wali bimbel QUALIFY.');
    $response->assertSee('Total Siswa Aktif');
    $response->assertSee('Siswa per Jenjang');
    $response->assertSee('Ahmad Zulfikar Ramadhan');
    $response->assertSee('QLF-2026-089');
});

test('data siswa is reachable from its named route', function () {
    $this->get(route('admin.dataSiswa'))->assertStatus(200);
});

test('data siswa menu is active on the page', function () {
    $this->get(route('admin.dataSiswa'))
        ->assertStatus(200)
        ->assertSee('Data Siswa &amp; Kelas', false);
});
