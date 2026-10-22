<?php

test('pembayaran orang tua page returns a successful response', function () {
    $response = $this->get('/wali/pembayaran');

    $response->assertStatus(200);
    $response->assertSee('Pembayaran &amp; Tagihan Bimbingan', false);
    $response->assertSee('Sisa Tagihan yang Perlu Dibayar');
    $response->assertSee('Total Sudah Terbayar');
    $response->assertSee('Daftar Tagihan');
    $response->assertSee('Bayar Instan');
    $response->assertSee('Tagihan Perlu Dibayar');
    $response->assertSee('Kwitansi');
});

test('pembayaran orang tua is reachable from its named route', function () {
    $this->get(route('wali.pembayaran'))->assertStatus(200);
});
