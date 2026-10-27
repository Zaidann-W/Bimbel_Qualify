<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('manajemen materi returns a successful response', function () {
    $response = $this->get('/admin/manajemen-materi');

    $response->assertStatus(200);
    $response->assertSee('Manajemen Materi Ajar');
    $response->assertSee('Kelola repositori materi pelajaran, modul ajar, dan distribusi berkas untuk kelas siswa bimbel QUALIFY.', false);
    $response->assertSee('Total Materi Ajar');
    $response->assertSee('Kelas Terdistribusi');
    $response->assertSee('Mode Baca Online');
    $response->assertSee('Unggah Materi Pelajaran');
});

test('manajemen materi is reachable from its named route', function () {
    $this->get(route('admin.manajemenMateri'))->assertStatus(200);
});

test('manajemen materi menu is active on the page', function () {
    $this->get(route('admin.manajemenMateri'))
        ->assertStatus(200)
        ->assertSee('Manajemen Materi Ajar');
});

test('manajemen materi lists repository files with access mode', function () {
    $response = $this->get(route('admin.manajemenMateri'));

    $response->assertStatus(200);
    $response->assertSee('Bank Soal TPS UTBK SNBT — Penalaran Matematika');
    $response->assertSee('Modul Praktikum Listrik Statis');
    $response->assertSee('Ust. Ahmad Fauzi');
    $response->assertSee('Hanya Baca');
    $response->assertSee('Boleh Unduh');
});
