<?php

use App\Models\ClassGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));

    $this->periodId = DB::table('academic_periods')->insertGetId([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->classId = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $this->periodId,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('halaman kelas menampilkan daftar kelas dan jumlah siswa aktif', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aisyah Nurul',
        'education_level' => 'SMA',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => now()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.kelas'))
        ->assertOk()
        ->assertSee('12-IPA-1')
        ->assertSee('2026/2027')
        ->assertSee('1 / 20');
});

test('admin bisa menambah kelas', function () {
    $this->post(route('admin.kelas.store'), [
        'academic_period_id' => $this->periodId,
        'code' => '11-IPS-1',
        'education_level' => 'SMA',
        'grade' => '11',
        'major' => 'IPS',
        'capacity' => 25,
        'status' => 'aktif',
    ])->assertRedirect(route('admin.kelas'));

    $this->assertDatabaseHas('class_groups', ['code' => '11-IPS-1', 'capacity' => 25]);
});

test('kode kelas unik per periode akademik', function () {
    $this->post(route('admin.kelas.store'), [
        'academic_period_id' => $this->periodId,
        'code' => '12-IPA-1',
        'status' => 'aktif',
    ])->assertSessionHasErrors('code');
});

test('admin bisa memperbarui kelas', function () {
    $classGroup = ClassGroup::findOrFail($this->classId);

    $this->put(route('admin.kelas.update', $classGroup), [
        'academic_period_id' => $this->periodId,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 30,
        'status' => 'nonaktif',
    ])->assertRedirect(route('admin.kelas'));

    expect($classGroup->fresh()->capacity)->toBe(30);
    expect($classGroup->fresh()->status)->toBe('nonaktif');
});

test('admin bisa menghapus kelas tanpa siswa aktif', function () {
    $classGroup = ClassGroup::findOrFail($this->classId);

    $this->delete(route('admin.kelas.destroy', $classGroup))
        ->assertRedirect(route('admin.kelas'));

    $this->assertDatabaseMissing('class_groups', ['id' => $this->classId]);
});

test('kelas dengan siswa aktif tidak bisa dihapus', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Bagas Pratama',
        'education_level' => 'SMA',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => now()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $classGroup = ClassGroup::findOrFail($this->classId);

    $this->from(route('admin.kelas'))
        ->delete(route('admin.kelas.destroy', $classGroup))
        ->assertRedirect(route('admin.kelas'))
        ->assertSessionHasErrors('kelas');

    $this->assertDatabaseHas('class_groups', ['id' => $this->classId]);
});

test('form tambah dan edit kelas bisa dibuka', function () {
    $this->get(route('admin.kelas.create'))->assertOk()->assertSee('Tambah Kelas Baru');
    $this->get(route('admin.kelas.edit', $this->classId))->assertOk()->assertSee('Edit Kelas')->assertSee('12-IPA-1');
});
