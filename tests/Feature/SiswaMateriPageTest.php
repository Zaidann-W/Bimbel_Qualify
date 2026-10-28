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

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $this->actingAs($this->user);

    $periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $kelas = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $siswa = Student::create([
        'user_id' => $this->user->id,
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    $berkas = File::create([
        'original_name' => 'modul-08.pdf',
        'path' => 'materials/modul-08.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 4404019,
    ]);

    $material = Material::create([
        'title' => 'Modul 08: Trik Cepat Integral & Limit Aljabar',
        'description' => 'Metode Huruf, L\'Hopital, rumus cepat limit wajib.',
        'subject_id' => $mapel->id,
        'file_id' => $berkas->id,
        'status' => 'terbit',
        'published_at' => now(),
    ]);

    $material->classGroups()->attach($kelas->id);
});

test('materi pembelajaran siswa page returns a successful response', function () {
    $this->get('/siswa/materi')
        ->assertStatus(200)
        ->assertSee('Katalog & Bank Modul Belajar Digital', false)
        ->assertSee('Modul 08: Trik Cepat Integral & Limit Aljabar')
        ->assertSee('Baca Modul')
        ->assertSee('Perlindungan Hak Cipta & Hak Use', false);
});

test('materi pembelajaran siswa is reachable from its named route', function () {
    $this->get(route('siswa.materi'))->assertStatus(200);
});

test('materi pembelajaran menampilkan empty state tanpa data', function () {
    Material::query()->delete();
    $this->get('/siswa/materi')
        ->assertStatus(200)
        ->assertSee('Belum ada modul yang dirilis untuk kelasmu.');
});
