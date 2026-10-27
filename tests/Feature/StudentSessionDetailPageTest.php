<?php

use App\Models\SessionMaterial;
use App\Models\Student;
use App\Models\StudySession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->studentUser = User::factory()->create(['role' => 'siswa']);

    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'full_name' => 'Aisyah Putri Rahmadani',
        'status' => 'aktif',
    ]);

    $this->studySession = StudySession::factory()->create([
        'title' => 'Kalkulus & Aplikasi Integral: Luas & Volume Benda Putar',
        'session_number' => 14,
        'total_sessions' => 24,
    ]);

    SessionMaterial::factory()->for($this->studySession)->create([
        'title' => 'Modul #11: Aplikasi Integral',
        'file_type' => 'PDF',
        'file_size' => '4.2 MB',
        'metadata' => 'Edisi Cetak 2025',
    ]);

    SessionMaterial::factory()->slide()->for($this->studySession)->create([
        'title' => 'Slide Interaktif Smart Board Eureka',
    ]);
});

test('siswa dapat melihat detail sesi belajar beserta materinya', function () {
    $response = $this->actingAs($this->studentUser)
        ->get(route('siswa.jadwal.show', $this->studySession));

    $response->assertOk()
        ->assertSee('Kalkulus &amp; Aplikasi Integral: Luas &amp; Volume Benda Putar', false)
        ->assertSee('Sesi ke-14 dari 24')
        ->assertSee('15.30 – 17.00 WIB')
        ->assertSee('Presensi RFID/Dibuka mulai 16.15 WIB')
        ->assertSee('Ruangan Eureka')
        ->assertSee('Kak Hendra Wijaya, M.Sc')
        ->assertSee('4.96')
        ->assertSee('320 ulasan')
        ->assertSee('Modul #11: Aplikasi Integral')
        ->assertSee('Format PDF • 4.2 MB • Edisi Cetak 2025')
        ->assertSee('Slide Interaktif Smart Board Eureka')
        ->assertSee('Buka Slide')
        ->assertSee('Ajukan Formulir Izin');
});

test('status presensi siswa dirender pada halaman detail sesi', function () {
    $this->studySession->attendances()->create([
        'student_id' => $this->student->id,
        'attendance_status' => 'Hadir',
        'check_in_time' => today()->setTime(15, 45),
    ]);

    $response = $this->actingAs($this->studentUser)
        ->get(route('siswa.jadwal.show', $this->studySession));

    $response->assertOk()
        ->assertSee('Status Presensi')
        ->assertSee('Hadir')
        ->assertSee('Check-in RFID pukul 15.45 WIB');
});

test('halaman jadwal siswa menampilkan sesi hari ini', function () {
    $response = $this->actingAs($this->studentUser)->get(route('siswa.jadwal'));

    $response->assertOk()
        ->assertSee('Kalkulus &amp; Aplikasi Integral: Luas &amp; Volume Benda Putar', false);
});

test('detail sesi belajar mengembalikan 404 untuk sesi yang tidak ada', function () {
    $this->actingAs($this->studentUser)
        ->get('/siswa/jadwal/9999')
        ->assertNotFound();
});

test('accessor sesi memformat data tampilan dengan benar', function () {
    expect($this->studySession->date_label)->toBe('Hari Ini')
        ->and($this->studySession->time_range_label)->toBe('15.30 – 17.00 WIB')
        ->and($this->studySession->rfid_checkin_label)->toBe('16.15 WIB')
        ->and($this->studySession->tutor_initials)->toBe('HW')
        ->and($this->studySession->room_base_name)->toBe('Ruangan Eureka')
        ->and($this->studySession->room_floor_label)->toBe('(Lantai 2)');
});

test('materi slide dikenali sebagai akses online', function () {
    $slide = $this->studySession->materials()->where('file_type', 'Slide')->first();

    expect($slide->is_online)->toBeTrue()
        ->and($slide->detail_label)->toBe('Akses Online • Live Anotasi saat sesi');
});
