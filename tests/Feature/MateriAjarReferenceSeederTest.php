<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\Subject;
use Database\Seeders\MateriAjarReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seeder referensi mengisi daftar mata pelajaran', function () {
    $this->seed(MateriAjarReferenceSeeder::class);

    expect(Subject::count())->toBeGreaterThan(0)
        ->and(Subject::where('name', 'Matematika')->exists())->toBeTrue();
});

test('seeder referensi mengisi kelas aktif pada periode berjalan', function () {
    AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $this->seed(MateriAjarReferenceSeeder::class);

    expect(ClassGroup::active()->count())->toBeGreaterThan(0)
        ->and(ClassGroup::active()->where('education_level', 'SMA')->exists())->toBeTrue();
});

test('seeder referensi mengisi rombel dari kelas 1 SD sampai kelas 12 SMA', function () {
    $this->seed(MateriAjarReferenceSeeder::class);

    expect(ClassGroup::where('education_level', 'SD')->where('grade', '1')->exists())->toBeTrue()
        ->and(ClassGroup::where('education_level', 'SD')->where('grade', '6')->exists())->toBeTrue()
        ->and(ClassGroup::where('education_level', 'SMP')->where('grade', '7')->exists())->toBeTrue()
        ->and(ClassGroup::where('education_level', 'SMP')->where('grade', '9')->exists())->toBeTrue()
        ->and(ClassGroup::where('education_level', 'SMA')->where('grade', '10')->exists())->toBeTrue()
        ->and(ClassGroup::where('education_level', 'SMA')->where('grade', '12')->exists())->toBeTrue();
});

test('seeder referensi dapat dijalankan berulang tanpa menggandakan data', function () {
    $this->seed(MateriAjarReferenceSeeder::class);
    $jumlahMapel = Subject::count();
    $jumlahKelas = ClassGroup::count();

    $this->seed(MateriAjarReferenceSeeder::class);

    expect(Subject::count())->toBe($jumlahMapel)
        ->and(ClassGroup::count())->toBe($jumlahKelas);
});
