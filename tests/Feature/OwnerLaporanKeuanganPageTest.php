<?php

test('laporan keuangan owner page returns a successful response', function () {
    $response = $this->get('/owner/laporan-keuangan');

    $response->assertStatus(200);
    $response->assertSee('Laporan Arus Keuangan Eksekutif');
    $response->assertSee('Sumber Pemasukan');
    $response->assertSee('Alokasi Beban Operasional');
    $response->assertSee('Ringkasan Tren 6 Bulan Terakhir');
    $response->assertSee('Data keuangan telah diaudit internal', false);
});

test('laporan keuangan owner page shows the period totals', function () {
    $response = $this->get(route('owner.laporanKeuangan'));

    $response->assertStatus(200);
    $response->assertSee('268.000.000');
    $response->assertSee('83.500.000');
    $response->assertSee('184.500.000');
    $response->assertSee('Total Masuk');
    $response->assertSee('Total Beban');
});
