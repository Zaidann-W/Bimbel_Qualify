<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'siswa']));
});

test('detail pertemuan siswa page returns a successful response', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200);
    $response->assertSee('Jadwal Belajar Siswa');
    $response->assertSee('Sesi Berjalan Hari Ini');
    $response->assertSee('Kalkulus &amp; Integral Lanjutan', false);
    $response->assertSee('Termokimia &amp; Kesetimbangan Kimia', false);
});

test('detail pertemuan siswa is reachable from its named route', function () {
    $this->get(route('siswa.jadwal.detail'))->assertStatus(200);
});
