<?php

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('profil tentor page returns a successful response', function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $response = $this->get('/tentor/profil');

    $response->assertStatus(200);
    $response->assertSee('Profil Pengajar & Akun Tentor', false);
    $response->assertSee('Informasi Pribadi & Kontak', false);
    $response->assertSee('Preferensi Mengajar & Notifikasi', false);
});

test('profil tentor is reachable from the sidebar route name', function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $this->get(route('tentor.profil'))->assertStatus(200);
});

test('profil tab tetap tampil untuk tentor yang belum punya staff profile', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    expect(StaffProfile::where('user_id', $user->id)->exists())->toBeFalse();

    $this->actingAs($user)
        ->get(route('tentor.profil'))
        ->assertOk()
        ->assertSee('Pendidikan Terakhir', false);
});

test('tentor tanpa staff profile bisa menyimpan profil dari form edit', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $this->actingAs($user)
        ->put(route('tentor.profil.update'), [
            'full_name_with_title' => 'Kak Uji Coba, S.Pd',
            'job_title' => 'Matematika',
            'education' => 'S1 Pendidikan Matematika',
            'address' => 'Jl. Contoh No. 1, Bandung',
            'phone' => '081234567890',
        ])
        ->assertSessionHas('success');

    $profile = StaffProfile::where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull()
        ->and($profile->education)->toBe('S1 Pendidikan Matematika');
});
