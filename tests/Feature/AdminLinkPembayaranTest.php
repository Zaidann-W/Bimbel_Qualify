<?php

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
});

function pendaftaranDiterima(array $overrides = []): Application
{
    return Application::create(array_merge([
        'registration_number' => 'REG-2026-0001',
        'full_name' => 'Budi Santoso',
        'phone_wa' => '081234567890',
        'email' => 'wali@example.com',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
    ], $overrides));
}

test('admin membuat link pembayaran xendit dan menyimpan url invoice', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_123',
            'invoice_url' => 'https://invoice.xendit.co/inv_123',
        ]),
    ]);

    $app = pendaftaranDiterima();

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->xendit_invoice_id)->toBe('inv_123')
        ->and($app->xendit_invoice_url)->toBe('https://invoice.xendit.co/inv_123');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.xendit.co/v2/invoices'
            && $request['payer_email'] === 'wali@example.com'
            && $request['redirect_url'] === route('pendaftaran.buatAkun', 'REG-2026-0001');
    });
});

test('link pembayaran memakai email cadangan saat pendaftar belum mengisi email', function () {
    config(['xendit.fallback_payer_email' => 'cadangan@example.com']);

    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_456',
            'invoice_url' => 'https://invoice.xendit.co/inv_456',
        ]),
    ]);

    $app = pendaftaranDiterima(['email' => null]);

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertSessionHas('success');

    Http::assertSent(fn (Request $request) => $request['payer_email'] === 'cadangan@example.com');
});

test('gagal membuat link pembayaran menyimpan pesan error di session', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'message' => 'Invalid input data. Please check your request',
            'error_code' => 'API_VALIDATION_ERROR',
            'errors' => [
                ['field' => ['payer_email'], 'messages' => ['"payer_email" is not allowed to be empty']],
            ],
        ], 400),
    ]);

    $app = pendaftaranDiterima();

    $this->actingAs($this->admin)
        ->from(route('admin.pendaftaran.detail', $app->registration_number))
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHasErrors('generate_link');

    $app->refresh();
    expect($app->xendit_invoice_url)->toBeNull();
});

test('pesan error pembuatan link pembayaran tampil di halaman detail', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'message' => 'Invalid input data. Please check your request',
            'error_code' => 'API_VALIDATION_ERROR',
            'errors' => [
                ['field' => ['payer_email'], 'messages' => ['"payer_email" is not allowed to be empty']],
            ],
        ], 400),
    ]);

    $app = pendaftaranDiterima();

    $response = $this->actingAs($this->admin)
        ->from(route('admin.pendaftaran.detail', $app->registration_number))
        ->followingRedirects()
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number));

    $response->assertOk()
        ->assertSee('Gagal membuat link pembayaran Xendit.')
        ->assertSee('Invalid input data');
});

test('pesan berhasil dari pembuatan link tampil di halaman detail', function () {
    Http::fake([
        'api.xendit.co/*' => Http::response([
            'id' => 'inv_789',
            'invoice_url' => 'https://invoice.xendit.co/inv_789',
        ]),
    ]);

    $app = pendaftaranDiterima();

    $response = $this->actingAs($this->admin)
        ->followingRedirects()
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number));

    $response->assertOk()
        ->assertSee('Link pembayaran Xendit berhasil dibuat.')
        ->assertSee('https://invoice.xendit.co/inv_789');
});

test('link pembayaran tidak bisa dibuat untuk pendaftaran yang belum disetujui', function () {
    Http::fake();

    $app = pendaftaranDiterima(['status' => 'menunggu_review']);

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertSessionHasErrors('generate_link');

    Http::assertNothingSent();
});

test('link pembayaran tidak bisa dibuat ulang untuk pembayaran yang sudah lunas', function () {
    Http::fake();

    $app = pendaftaranDiterima(['payment_status' => 'lunas']);

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertSessionHasErrors('generate_link');

    Http::assertNothingSent();
});

test('admin dapat mengisi email pendaftar yang belum punya email', function () {
    $app = pendaftaranDiterima(['email' => null]);

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.email', $app->registration_number), [
            'email' => 'baru@example.com',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    expect($app->refresh()->email)->toBe('baru@example.com');
});

test('email pendaftar harus valid', function () {
    $app = pendaftaranDiterima();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.email', $app->registration_number), [
            'email' => 'bukan-email',
        ])
        ->assertSessionHasErrors('email');

    expect($app->refresh()->email)->toBe('wali@example.com');
});

test('admin dapat menandai pembayaran pendaftaran sebagai lunas', function () {
    $app = pendaftaranDiterima(['payment_status' => 'menunggu_verifikasi']);

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.pembayaran', $app->registration_number), [
            'payment_status' => 'lunas',
            'review_note' => 'Sudah dicek mutasi.',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->payment_status)->toBe('lunas')
        ->and($app->payment_verified_at)->not->toBeNull()
        ->and($app->payment_verified_by)->toBe($this->admin->id);
});

test('pengguna non admin tidak bisa membuat link pembayaran', function () {
    Http::fake();

    $app = pendaftaranDiterima();
    $ortu = User::factory()->create(['role' => 'ortu', 'status' => 'aktif']);

    $this->actingAs($ortu)
        ->post(route('admin.pendaftaran.link-pembayaran', $app->registration_number))
        ->assertRedirect(route('ortu.dashboard'));

    Http::assertNothingSent();
});
