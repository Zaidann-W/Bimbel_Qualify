<?php

test('keuangan bulanan manager page returns a successful response', function () {
    $response = $this->get('/manager/keuangan-bulanan');

    $response->assertStatus(200);
    $response->assertSee('Laporan dan Pemantauan', false);
    $response->assertSee('Keuangan Bulanan');
    $response->assertSee('Arus Kas Mingguan (Cashflow)');
    $response->assertSee('Distribusi Anggaran');
    $response->assertSee('Rekapitulasi Kategori Keuangan');
});

test('keuangan bulanan manager page shows the monthly totals', function () {
    $response = $this->get(route('manager.keuanganBulanan'));

    $response->assertStatus(200);
    $response->assertSee('142.850.000');
    $response->assertSee('58.200.000');
    $response->assertSee('84.650.000');
    $response->assertSee('14.500.000');
    $response->assertSee('Rp 201.050.000');
    $response->assertSee('+18.4%');
});
