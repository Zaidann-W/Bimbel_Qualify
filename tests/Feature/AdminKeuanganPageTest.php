<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('keuangan returns a successful response', function () {
    $response = $this->get('/admin/keuangan');

    $response->assertStatus(200);
    $response->assertSee('Pencatatan Keuangan Harian');
    $response->assertSee('Arus kas bimbel, pembayaran SPP siswa, dan pengeluaran operasional hari ini.', false);
    $response->assertSee('Buku Kas Hari Ini');
    $response->assertSee('Kas &amp; Rekening', false);
    $response->assertSee('Tagihan SPP Belum Lunas', false);
});

test('keuangan is reachable from its named route', function () {
    $this->get(route('admin.keuangan'))->assertStatus(200);
});

test('keuangan menu is active on the page', function () {
    $this->get(route('admin.keuangan'))
        ->assertStatus(200)
        ->assertSee('Keuangan &amp; Transaksi', false);
});

test('keuangan lists daily transactions and unpaid bills', function () {
    $response = $this->get(route('admin.keuangan'));

    $response->assertStatus(200);
    $response->assertSee('SPP Bulan Oktober 2024');
    $response->assertSee('Pendaftaran &amp; DP Tryout UTBK', false);
    $response->assertSee('Percetakan Prima Jaya');
    $response->assertSee('Fajar Ramadhan');
    $response->assertSee('Kirana Naura');
    $response->assertSee('Jatuh tempo hari ini');
});
