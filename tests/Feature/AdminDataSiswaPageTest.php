<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('data siswa returns a successful response', function () {
    DB::table('students')->insert([
        'full_name' => 'Ahmad Zulfikar Ramadhan',
        'nis' => 'QLF-2026-089',
        'education_level' => 'SMA',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->get('/admin/data-siswa');

    $response->assertStatus(200);
    $response->assertSee('Data Siswa');
    $response->assertSee('Database induk siswa terdaftar dan informasi kontak wali bimbel QUALIFY.');
    $response->assertSee('Total Siswa Aktif');
    $response->assertSee('Siswa per Jenjang');
    $response->assertSee('Ahmad Zulfikar Ramadhan');
    $response->assertSee('QLF-2026-089');
});

test('data siswa is reachable from its named route', function () {
    $this->get(route('admin.dataSiswa'))->assertStatus(200);
});

test('data siswa menu is active on the page', function () {
    $this->get(route('admin.dataSiswa'))
        ->assertStatus(200)
        ->assertSee('Data Siswa &amp; Kelas', false);
});
