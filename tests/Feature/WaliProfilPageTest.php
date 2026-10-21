<?php

test('profil orang tua page returns a successful response', function () {
    $response = $this->get('/wali/profil');

    $response->assertStatus(200);
    $response->assertSee('Profil Saya (Wali Murid)');
    $response->assertSee('Ir. Hendra Gunawan');
    $response->assertSee('WAL-2024-0419');
    $response->assertSee('Data Diri');
    $response->assertSee('Putra-Putri Terhubung');
    $response->assertSee('Aisyah Putri Azzahra');
    $response->assertSee('Kata Sandi Akun');
    $response->assertSee('Notifikasi Otomatis WhatsApp');
});

test('profil orang tua is reachable from its named route', function () {
    $this->get(route('wali.profil'))->assertStatus(200);
});
