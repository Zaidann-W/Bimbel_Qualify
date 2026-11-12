<?php

use App\Models\Student;
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

    $this->classA = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $this->periodId,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->classB = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $this->periodId,
        'code' => '11-IPA-2',
        'education_level' => 'SMA',
        'grade' => '11',
        'capacity' => 20,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('halaman data siswa menampilkan siswa dari database', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aisyah Nurul Hikmah',
        'nis' => '260001',
        'education_level' => 'SMA',
        'school_name' => 'SMAN 1 Yogyakarta',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('guardians')->insert(['name' => 'Bapak Hikmah', 'phone_wa' => '08123456789', 'created_at' => now(), 'updated_at' => now()]);
    $guardianId = DB::table('guardians')->where('name', 'Bapak Hikmah')->value('id');

    DB::table('student_guardians')->insert([
        'student_id' => $studentId,
        'guardian_id' => $guardianId,
        'relationship' => 'wali',
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classA,
        'started_on' => now()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.dataSiswa'))
        ->assertOk()
        ->assertSee('Aisyah Nurul Hikmah')
        ->assertSee('260001')
        ->assertSee('Bapak Hikmah')
        ->assertSee('12-IPA-1');
});

test('halaman data siswa mendukung pencarian dan ekspor csv', function () {
    DB::table('students')->insert([
        ['full_name' => 'Aisyah Nurul', 'education_level' => 'SMA', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
        ['full_name' => 'Bagas Pratama', 'education_level' => 'SMP', 'status' => 'aktif', 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->get(route('admin.dataSiswa', ['q' => 'Bagas']))
        ->assertOk()
        ->assertSee('Bagas Pratama')
        ->assertDontSee('Aisyah Nurul');

    $this->get(route('admin.dataSiswa', ['export' => 'csv']))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('admin bisa menambah siswa beserta wali dan kelas', function () {
    $this->post(route('admin.dataSiswa.store'), [
        'full_name' => 'Citra Dewi',
        'nickname' => 'Citra',
        'birth_date' => '2008-05-14',
        'kelas' => 'sma-12',
        'school_name' => 'SMAN 1 Yogyakarta',
        'phone_wa' => '08129876543',
        'address' => 'Jl. Merdeka No. 10',
        'nis' => '260099',
        'status' => 'aktif',
        'guardian_name' => 'Ibu Dewi',
        'guardian_phone' => '08129876544',
        'group_wa' => '08129876545',
        'sesi' => 'sore',
        'class_group_id' => $this->classA,
    ])->assertRedirect(route('admin.dataSiswa'));

    $student = Student::where('full_name', 'Citra Dewi')->firstOrFail();

    expect($student->guardians()->first()?->name)->toBe('Ibu Dewi');
    expect($student->enrollments()->where('status', 'aktif')->value('class_group_id'))->toBe($this->classA);
    expect($student->education_level)->toBe('SMA');
    expect($student->school_grade)->toBe('12');
    expect($student->group_wa)->toBe('08129876545');
    expect($student->studyShift?->name)->toBe('Sesi Sore');
});

test('admin bisa memperbarui siswa dan memindahkan kelas', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Dimas Saputra',
        'education_level' => 'SMA',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classA,
        'started_on' => now()->subMonth()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $student = Student::findOrFail($studentId);

    $this->put(route('admin.dataSiswa.update', $student), [
        'full_name' => 'Dimas Saputra',
        'nickname' => 'Dimas',
        'birth_date' => '2008-01-20',
        'kelas' => 'sma-11',
        'school_name' => 'SMAN 2 Yogyakarta',
        'phone_wa' => '0812 3456 7890',
        'address' => 'Jl. Melati No. 3',
        'status' => 'cuti',
        'guardian_name' => 'Bapak Dimas',
        'guardian_phone' => '08129876541',
        'group_wa' => '08129876542',
        'sesi' => 'malam',
        'class_group_id' => $this->classB,
    ])->assertRedirect(route('admin.dataSiswa'));

    expect($student->fresh()->status)->toBe('cuti');
    expect($student->fresh()->school_grade)->toBe('11');
    expect($student->fresh()->phone_wa)->toBe('081234567890');
    expect($student->fresh()->studyShift?->name)->toBe('Sesi Malam');
    expect(DB::table('enrollments')->where('student_id', $studentId)->where('class_group_id', $this->classA)->value('status'))->toBe('pindah');
    expect(DB::table('enrollments')->where('student_id', $studentId)->where('class_group_id', $this->classB)->value('status'))->toBe('aktif');
});

test('admin bisa menghapus siswa dan menutup kelas aktifnya', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Eka Lestari',
        'education_level' => 'SMP',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classA,
        'started_on' => now()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $student = Student::findOrFail($studentId);

    $this->delete(route('admin.dataSiswa.destroy', $student))
        ->assertRedirect(route('admin.dataSiswa'));

    expect(Student::find($studentId))->toBeNull();
    expect(Student::withTrashed()->find($studentId)?->trashed())->toBeTrue();
    expect(DB::table('enrollments')->where('student_id', $studentId)->value('status'))->toBe('keluar');
});

test('validasi menolak siswa tanpa nama dan kelas', function () {
    $this->post(route('admin.dataSiswa.store'), [
        'status' => 'aktif',
    ])->assertSessionHasErrors(['full_name', 'kelas', 'nickname', 'birth_date', 'school_name', 'phone_wa', 'address', 'guardian_name', 'guardian_phone', 'group_wa', 'sesi']);
});

test('halaman detail siswa menampilkan identitas dan riwayat kelas', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Fajar Nugroho',
        'nis' => '260123',
        'education_level' => 'SMA',
        'school_name' => 'SMAN 2 Yogyakarta',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classA,
        'started_on' => now()->subMonth()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.dataSiswa.show', $studentId))
        ->assertOk()
        ->assertSee('Fajar Nugroho')
        ->assertSee('260123')
        ->assertSee('SMAN 2 Yogyakarta')
        ->assertSee('Riwayat Kelas')
        ->assertSee('12-IPA-1');
});

test('form tambah dan edit siswa bisa dibuka', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Gita Permata',
        'education_level' => 'SD',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.dataSiswa.create'))->assertOk()->assertSee('Tambah Siswa Baru');
    $this->get(route('admin.dataSiswa.edit', $studentId))->assertOk()->assertSee('Edit Data Siswa')->assertSee('Gita Permata');
});

test('form data siswa memakai field yang sama dengan formulir pendaftaran', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Hana Kusuma',
        'nickname' => 'Hana',
        'education_level' => 'SMA',
        'school_grade' => '12',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.dataSiswa.edit', $studentId))
        ->assertOk()
        ->assertSee('Data Calon Siswa')
        ->assertSee('Nama Lengkap Siswa')
        ->assertSee('Nama Panggilan Siswa')
        ->assertSee('Tanggal Lahir Siswa')
        ->assertSee('Kelas T.A (Tahun Ajaran)')
        ->assertSee('Asal Sekolah')
        ->assertSee('No. WA Siswa')
        ->assertSee('Alamat Tempat Tinggal')
        ->assertSee('Kontak Orang Tua / Wali')
        ->assertSee('Nama Orang Tua / Wali')
        ->assertSee('No. WA Wali')
        ->assertSee('No. yang Dimasukkan ke Grup')
        ->assertSee('Pilihan Sesi Belajar')
        ->assertSee('Sesi Sore')
        ->assertSee('Sesi Malam')
        ->assertSee('SMA Kelas 12')
        ->assertSee('Data Internal Admin');
});
