<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentGuardian;
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
        'phone_wa' => '0812-9843-2210',
        'address' => 'Jl. Tebet Barat Dalam No. 42',
        'school_name' => 'SMAN 8 Jakarta',
        'birth_date' => '2007-05-14',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $wali = Guardian::create([
        'name' => 'Ir. Bambang Azzahra',
        'phone_wa' => '0811-8820-4491',
        'email' => 'bambang@parent.qualify.id',
    ]);

    StudentGuardian::create([
        'student_id' => $siswa->id,
        'guardian_id' => $wali->id,
        'relationship' => 'ayah',
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
    ]);
});

test('profil siswa page returns a successful response', function () {
    $this->get('/siswa/profil')
        ->assertStatus(200)
        ->assertSee('Profil Siswa')
        ->assertSee('Data Diri Siswa')
        ->assertSee('Kontak Orang Tua / Wali')
        ->assertSee('Akun Pembelajaran')
        ->assertSee('Simpan Perubahan')
        ->assertSee('Data Terverifikasi Sekolah');
});

test('profil siswa is reachable from its named route', function () {
    $this->get(route('siswa.profil'))->assertStatus(200);
});

test('profil menampilkan data siswa dan wali dari database', function () {
    $this->get('/siswa/profil')
        ->assertStatus(200)
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('0098123471')
        ->assertSee('SMAN 8 Jakarta')
        ->assertSee('Ir. Bambang Azzahra')
        ->assertSee('ayah')
        ->assertSee('0811-8820-4491');
});
