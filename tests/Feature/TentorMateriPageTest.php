<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\File;
use App\Models\Material;
use App\Models\StaffProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    $this->user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $this->actingAs($this->user);

    $this->staff = StaffProfile::create([
        'user_id' => $this->user->id,
        'employee_number' => 'T009',
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    $periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $this->kelas = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $this->kelasLain = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-2',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $this->mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $this->mapelLain = Subject::create(['code' => 'FIS', 'name' => 'Fisika']);

    ClassSession::create([
        'class_group_id' => $this->kelas->id,
        'subject_id' => $this->mapel->id,
        'staff_id' => $this->staff->id,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'status' => 'terjadwal',
    ]);

    Storage::disk('local')->put('materi/modul-08.pdf', 'isi modul matematika');

    $this->materi = Material::create([
        'title' => 'Modul 08: Trik Cepat Integral & Limit Aljabar',
        'description' => 'Metode Huruf dan rumus cepat limit.',
        'subject_id' => $this->mapel->id,
        'file_id' => File::create([
            'original_name' => 'modul-08.pdf',
            'path' => 'materi/modul-08.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 4404019,
        ])->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $this->materi->classGroups()->attach($this->kelas->id);
});

test('halaman materi tentor menampilkan modul kelas yang diampu', function () {
    $this->get(route('tentor.materi'))
        ->assertStatus(200)
        ->assertSee('Bank Materi')
        ->assertSee('Modul 08: Trik Cepat Integral & Limit Aljabar')
        ->assertSee(route('tentor.materi.unduh', $this->materi), false)
        ->assertSee('Buka / Unduh Modul');
});

test('halaman materi tentor hanya menampilkan kelas yang diampu', function () {
    $this->materi->classGroups()->sync([$this->kelasLain->id]);

    $this->get(route('tentor.materi'))
        ->assertStatus(200)
        ->assertDontSee('Modul 08: Trik Cepat Integral & Limit Aljabar')
        ->assertSee('Belum ada modul ajar untuk kelas yang Anda ampu.');
});

test('filter mata pelajaran menyaring daftar modul tentor', function () {
    $materiFisika = Material::create([
        'title' => 'Fisika Modern: Efek Fotolistrik',
        'subject_id' => $this->mapelLain->id,
        'file_id' => File::create([
            'original_name' => 'fisika-modern.pdf',
            'path' => 'materi/fisika-modern.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1024,
        ])->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);
    $materiFisika->classGroups()->attach($this->kelas->id);

    $this->get(route('tentor.materi', ['subject_id' => $this->mapelLain->id]))
        ->assertStatus(200)
        ->assertSee('Fisika Modern: Efek Fotolistrik')
        ->assertDontSee('Modul 08: Trik Cepat Integral & Limit Aljabar');
});

test('pencarian judul menyaring daftar modul tentor', function () {
    $this->get(route('tentor.materi', ['q' => 'Integral']))
        ->assertStatus(200)
        ->assertSee('Modul 08: Trik Cepat Integral & Limit Aljabar');

    $this->get(route('tentor.materi', ['q' => 'Fotolistrik']))
        ->assertStatus(200)
        ->assertSee('Belum ada modul ajar untuk kelas yang Anda ampu.');
});

test('tentor dapat mengunduh modul yang mengizinkan unduhan', function () {
    $this->get(route('tentor.materi.unduh', $this->materi))
        ->assertStatus(200)
        ->assertDownload('modul-08.pdf');
});

test('tentor tidak dapat mengunduh modul kelas yang tidak diampu', function () {
    $this->materi->classGroups()->sync([$this->kelasLain->id]);

    $this->get(route('tentor.materi.unduh', $this->materi))->assertForbidden();
});

test('modul yang hanya dibaca tidak dapat diunduh tentor', function () {
    $this->materi->update(['allow_download' => false]);

    $this->get(route('tentor.materi.unduh', $this->materi))->assertForbidden();
});

test('modul berstatus draf tidak dapat diunduh tentor', function () {
    $this->materi->update(['status' => 'draf']);

    $this->get(route('tentor.materi.unduh', $this->materi))->assertNotFound();
});

test('tentor tanpa profil staf melihat daftar modul kosong', function () {
    $userBaru = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $this->actingAs($userBaru)
        ->get(route('tentor.materi'))
        ->assertStatus(200)
        ->assertDontSee('Modul 08: Trik Cepat Integral & Limit Aljabar')
        ->assertSee('Belum ada modul ajar untuk kelas yang Anda ampu.');
});
