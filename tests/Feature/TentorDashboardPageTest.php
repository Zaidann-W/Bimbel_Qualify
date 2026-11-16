<?php

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard tentor menampilkan kartu sambutan, hari, dan jam', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Tentor Matematika']);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang, Tentor Matematika!', false)
        ->assertSee('Hari Ini', false)
        ->assertSee(now()->locale('id')->translatedFormat('l'), false)
        ->assertSee(now()->locale('id')->translatedFormat('j F Y'), false)
        ->assertSee(now()->format('H:i').' WIB', false);
});

test('label jabatan tentor diambil dari staff profile', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    StaffProfile::create([
        'user_id' => $user->id,
        'employee_number' => 'T009',
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Master Tentor Matematika', false);
});

test('dashboard tentor tanpa staff profile tetap memakai nama pengguna sebagai jabatan', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Tentor Matematika']);

    expect(StaffProfile::where('user_id', $user->id)->exists())->toBeFalse();

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang, Tentor Matematika!', false);
});
