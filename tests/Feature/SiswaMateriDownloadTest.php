<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\Enrollment;
use App\Models\File;
use App\Models\Material;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    $this->user = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $this->actingAs($this->user);

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

    $kelasLain = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-2',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $this->kelasLain = $kelasLain;

    $siswa = Student::create([
        'user_id' => $this->user->id,
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $this->kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $this->mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

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

test('siswa dapat mengunduh modul yang mengizinkan unduhan', function () {
    $response = $this->get(route('siswa.materi.unduh', $this->materi));

    $response->assertStatus(200);
    $response->assertDownload('modul-08.pdf');
});

test('siswa tidak dapat mengunduh modul yang hanya dibaca', function () {
    $this->materi->update(['allow_download' => false]);

    $this->get(route('siswa.materi.unduh', $this->materi))->assertForbidden();
});

test('siswa dari kelas lain tidak dapat mengunduh modul', function () {
    $this->materi->classGroups()->sync([$this->kelasLain->id]);

    $this->get(route('siswa.materi.unduh', $this->materi))->assertForbidden();
});

test('modul berstatus draf tidak dapat diunduh siswa', function () {
    $this->materi->update(['status' => 'draf']);

    $this->get(route('siswa.materi.unduh', $this->materi))->assertNotFound();
});

test('halaman materi siswa menampilkan aksi sesuai hak akses', function () {
    $this->get(route('siswa.materi'))
        ->assertStatus(200)
        ->assertSee(route('siswa.materi.unduh', $this->materi), false)
        ->assertSee('Unduh Modul');

    $this->materi->update(['allow_download' => false]);

    $this->get(route('siswa.materi'))
        ->assertStatus(200)
        ->assertSee('Hanya Baca');
});

test('siswa tanpa kelas aktif tidak melihat modul kelas lain', function () {
    Enrollment::query()->delete();

    $this->get(route('siswa.materi'))
        ->assertStatus(200)
        ->assertDontSee('Modul 08: Trik Cepat Integral & Limit Aljabar')
        ->assertSee('Belum ada modul yang dirilis untuk kelasmu.');
});
