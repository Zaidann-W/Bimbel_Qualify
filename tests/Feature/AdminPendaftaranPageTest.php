<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('pendaftaran returns a successful response', function () {
    $response = $this->get('/admin/pendaftaran');

    $response->assertStatus(200);
    $response->assertSee('Verifikasi &amp; Pengelolaan Pendaftaran Siswa', false);
    $response->assertSee('Pemeriksaan formulir pendaftaran masuk, verifikasi berkas', false);
    $response->assertSee('Menunggu Verifikasi');
    $response->assertSee('Perlu Perbaikan');
    $response->assertSee('Diterima Bulan Ini');
    $response->assertSee('Ditolak / Batal');
});

test('pendaftaran is reachable from its named route', function () {
    $this->get(route('admin.pendaftaran'))->assertStatus(200);
});

test('pendaftaran menu is active on the page', function () {
    $this->get(route('admin.pendaftaran'))
        ->assertStatus(200)
        ->assertSee('Pendaftaran Siswa');
});

test('pendaftaran lists incoming registrations with status and action', function () {
    $response = $this->get(route('admin.pendaftaran'));

    $response->assertStatus(200);
    $response->assertSee('REG-2025-0891');
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Aisyah Nurul Hikmah');
    $response->assertSee('Menunggu Review');
    $response->assertSee('Perbaiki');
});

test('pendaftaran detail opens a registration by its code', function () {
    $response = $this->get(route('admin.pendaftaran.detail', 'REG-2025-0891'));

    $response->assertStatus(200);
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Pendaftaran Siswa');
});

test('guest cannot access pendaftaran page', function () {
    $this->app['auth']->forgetGuards();

    $this->get(route('admin.pendaftaran'))->assertRedirect(route('login'));
});
