<?php

test('dashboard owner page returns a successful response', function () {
    $response = $this->get('/owner');

    $response->assertStatus(200);
    $response->assertSee('Dashboard Ringkasan Operasional &amp; Bisnis', false);
    $response->assertSee('Kinerja Finansial &amp; Margin Arus Kas', false);
    $response->assertSee('Ringkasan Presensi');
    $response->assertSee('Distribusi Materi');
    $response->assertSee('Website &amp; Pendaftaran', false);
    $response->assertSee('Konsolidasi otomatis diverifikasi oleh sistem internal QUALIFY ERP', false);
});

test('dashboard owner page shows the period headline figures', function () {
    $response = $this->get(route('owner.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Rp 184.5M');
    $response->assertSee('Rp 268.000.000');
    $response->assertSee('Rp 83.500.000');
    $response->assertSee('96.4%');
    $response->assertSee('78 Modul');
    $response->assertSee('42 Siswa');
});

test('dashboard owner page marks its own menu entry as active', function () {
    $response = $this->get(route('owner.dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('owner.dashboard'), false);
    $response->assertSee('Executive Center');
});
