<?php

test('profil tentor page returns a successful response', function () {
    $response = $this->get('/tentor/profil');

    $response->assertStatus(200);
    $response->assertSee('Profil Pengajar &amp; Akun Tentor', false);
    $response->assertSee('Informasi Pribadi &amp; Kontak', false);
    $response->assertSee('Preferensi Mengajar &amp; Notifikasi', false);
});

test('profil tentor is reachable from the sidebar route name', function () {
    $this->get(route('tentor.profil'))->assertStatus(200);
});
