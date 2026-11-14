<?php

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function pendaftaranDenganInvoice(array $overrides = []): Application
{
    return Application::create(array_merge([
        'registration_number' => 'REG-2026-0001',
        'full_name' => 'Budi Santoso',
        'phone_wa' => '081234567890',
        'email' => 'wali@example.com',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
        'xendit_invoice_id' => 'inv_pendaftaran_001',
        'xendit_invoice_url' => 'https://checkout-staging.xendit.co/web/inv_pendaftaran_001',
        'xendit_external_id' => 'REG-REG-2026-0001-a1b2c3d4',
    ], $overrides));
}

test('halaman pembayaran mengarahkan ke pembuatan akun ketika invoice sudah dibayar', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_pendaftaran_001',
            'status' => 'PAID',
            'external_id' => 'REG-REG-2026-0001-a1b2c3d4',
        ]),
    ]);

    $app = pendaftaranDenganInvoice();

    $this->get(route('pendaftaran.bayar', $app->registration_number))
        ->assertRedirect(route('pendaftaran.buatAkun', $app->registration_number));
});

test('halaman buat akun menampilkan form ketika invoice sudah dibayar', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_pendaftaran_001',
            'status' => 'PAID',
            'external_id' => 'REG-REG-2026-0001-a1b2c3d4',
        ]),
    ]);

    $app = pendaftaranDenganInvoice();

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertOk()
        ->assertSee('Buat Akun Orang Tua');

    expect($app->refresh()->payment_status)->toBe('lunas');
});

test('endpoint status menyinkronkan dan melaporkan pembayaran lunas', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_pendaftaran_001',
            'status' => 'PAID',
            'external_id' => 'REG-REG-2026-0001-a1b2c3d4',
        ]),
    ]);

    $app = pendaftaranDenganInvoice();

    $this->getJson(route('pendaftaran.status', $app->registration_number))
        ->assertOk()
        ->assertJson([
            'lunas' => true,
            'payment_status' => 'lunas',
        ]);
});

test('invoice yang belum dibayar tidak menandai pembayaran lunas', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_pendaftaran_001',
            'status' => 'PENDING',
            'external_id' => 'REG-REG-2026-0001-a1b2c3d4',
        ]),
    ]);

    $app = pendaftaranDenganInvoice();

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertOk()
        ->assertSee('Menunggu Konfirmasi Pembayaran');

    expect($app->refresh()->payment_status)->toBe('belum_bayar');
});

test('gagal menjangkau Xendit tidak membuat halaman error', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response(['message' => 'Internal error'], 500),
    ]);

    $app = pendaftaranDenganInvoice();

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertOk()
        ->assertSee('Menunggu Konfirmasi Pembayaran');

    expect($app->refresh()->payment_status)->toBe('belum_bayar');
});

test('pendaftaran tanpa invoice Xendit tidak memanggil API Xendit', function () {
    Http::fake();

    $app = pendaftaranDenganInvoice([
        'xendit_invoice_id' => null,
        'xendit_invoice_url' => null,
    ]);

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertOk()
        ->assertSee('Menunggu Konfirmasi Pembayaran');

    Http::assertNothingSent();
});
