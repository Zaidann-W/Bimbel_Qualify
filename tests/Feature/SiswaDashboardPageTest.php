<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'siswa', 'status' => 'aktif']));
});

test('dashboard siswa page returns a successful response', function () {
    $response = $this->get('/siswa');

    $response->assertStatus(200);
    $response->assertSee('Halo, Aisyah! Semangat belajar hari ini');
    $response->assertSee('Jadwal Belajar Pekan Ini');
    $response->assertSee('Materi &amp; Modul Baru Rilis', false);
    $response->assertSee('Pengumuman Pergantian Jam');
});

test('dashboard siswa is reachable from its named route', function () {
    $this->get(route('siswa.dashboard'))->assertStatus(200);
});
