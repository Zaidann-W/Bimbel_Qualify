<?php

use App\Models\StudySession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'siswa']));

    StudySession::factory()->create([
        'title' => 'Kalkulus & Integral Lanjutan',
        'category_badge' => 'Matematika Saintek',
        'date' => today(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'room_name' => 'Ruang Eureka (Lantai 2)',
        'room_capacity' => 15,
        'tutor_name' => 'Kak Hendra Wijaya, M.Sc',
    ]);

    StudySession::factory()->create([
        'title' => 'Termokimia & Kesetimbangan Kimia',
        'category_badge' => 'Kimia Saintek',
        'date' => today()->addDays(3),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'room_name' => 'Ruang Volta (Lantai 2)',
        'tutor_name' => 'Dr Hartanto, M.Pd',
    ]);
});

test('detail pertemuan siswa page returns a successful response', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200);
    $response->assertSee('Jadwal Belajar Siswa');
    $response->assertSee('Sesi Berjalan Hari Ini');
    $response->assertSee('Kalkulus &amp; Integral Lanjutan', false);
    $response->assertSee('Termokimia &amp; Kesetimbangan Kimia', false);
    $response->assertSee('Kak Hendra Wijaya, M.Sc');
});

test('detail pertemuan siswa is reachable from its named route', function () {
    $this->get(route('siswa.jadwal.detail'))->assertStatus(200);
});

test('detail pertemuan siswa menampilkan statis sesi dari database', function () {
    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Matematika Saintek')
        ->assertSee('Kimia Saintek')
        ->assertSee('Sesi Berjalan')
        ->assertSee('Terjadwal');
});

test('detail pertemuan siswa menampilkan empty state tanpa data', function () {
    StudySession::query()->delete();

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Belum ada sesi belajar yang terjadwal.');
});
