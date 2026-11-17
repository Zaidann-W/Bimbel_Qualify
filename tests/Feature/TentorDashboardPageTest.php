<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Room;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard tentor menampilkan kartu sambutan, hari, dan jam', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Tentor Matematika']);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang, Tentor Matematika!', false)
        ->assertSee('Hari Ini', false)
        ->assertSee(now()->locale('id')->translatedFormat('l'), false)
        ->assertSee(now()->locale('id')->translatedFormat('j F Y'), false)
        ->assertSee(now()->format('H:i').' WIB', false);
});

test('label jabatan tentor diambil dari staff profile', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    StaffProfile::create([
        'user_id' => $user->id,
        'employee_number' => 'T009',
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Master Tentor Matematika', false);
});

test('dashboard tentor tanpa staff profile tetap memakai nama pengguna sebagai jabatan', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Tentor Matematika']);

    expect(StaffProfile::where('user_id', $user->id)->exists())->toBeFalse();

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang, Tentor Matematika!', false);
});

test('dashboard tentor menampilkan sesi mengajar hari ini dari class_sessions', function () {
    [$user, $staff] = tentorDenganProfil();
    $kelas = kelasUntukTesting();
    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $ruang = Room::create(['name' => 'Ruang Eureka', 'floor' => 'Lt. 2', 'status' => 'aktif']);
    $siswa = Student::create(['user_id' => $user->id, 'full_name' => 'Aisyah Putri', 'nisn' => '0098123456', 'status' => 'aktif']);
    Enrollment::create(['student_id' => $siswa->id, 'class_group_id' => $kelas->id, 'started_on' => '2026-07-14', 'status' => 'aktif']);

    $sesi = ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'room_id' => $ruang->id,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'topic' => 'Integral Substitusi & Parsial',
        'status' => 'terjadwal',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Jadwal Mengajar Hari Ini', false)
        ->assertSee('Matematika Saintek', false)
        ->assertSee('15.30', false)
        ->assertSee('12-IPA-1', false)
        ->assertSee('1 Sesi Terjadwal', false)
        ->assertSee(route('tentor.absensi', ['session_id' => $sesi->id]), false)
        ->assertSee(e(route('tentor.materi', [
            'subject_id' => $mapel->id,
            'class_group_id' => $kelas->id,
        ])), false);
});

test('dashboard tentor hanya menampilkan sesi miliknya sendiri', function () {
    [$user, $staff] = tentorDenganProfil();

    $kelas = kelasUntukTesting();
    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    $staffLain = StaffProfile::create([
        'user_id' => User::factory()->create(['role' => 'tentor', 'status' => 'aktif'])->id,
        'employee_number' => 'T010',
        'full_name_with_title' => 'Kak Rina',
        'job_title' => 'Master Tentor Fisika',
        'status' => 'aktif',
    ]);

    ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'session_date' => today()->toDateString(),
        'start_time' => '07:00:00',
        'end_time' => '08:30:00',
        'status' => 'terjadwal',
    ]);

    ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staffLain->id,
        'session_date' => today()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:30:00',
        'topic' => 'Hukum Newton',
        'status' => 'terjadwal',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('07.00', false)
        ->assertSee('08.30', false)
        ->assertDontSee('Hukum Newton', false)
        ->assertDontSee('10.30', false);
});

test('dashboard tentor tidak menampilkan sesi yang dibatalkan', function () {
    [$user, $staff] = tentorDenganProfil();
    $kelas = kelasUntukTesting();
    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'session_date' => today()->toDateString(),
        'start_time' => '07:00:00',
        'end_time' => '08:30:00',
        'topic' => 'Limit Fungsi',
        'status' => 'dibatalkan',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Belum ada jadwal mengajar untuk hari ini.', false)
        ->assertDontSee('Limit Fungsi', false);
});

test('status absensi pada dashboard menghitung sesi yang belum ada presensinya', function () {
    [$user, $staff] = tentorDenganProfil();
    $kelas = kelasUntukTesting();
    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $siswa = Student::create(['user_id' => $user->id, 'full_name' => 'Aisyah Putri', 'nisn' => '0098123456', 'status' => 'aktif']);
    Enrollment::create(['student_id' => $siswa->id, 'class_group_id' => $kelas->id, 'started_on' => '2026-07-14', 'status' => 'aktif']);

    $sesi = ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'session_date' => today()->toDateString(),
        'start_time' => '07:00:00',
        'end_time' => '08:30:00',
        'status' => 'terjadwal',
    ]);

    StudentAttendance::create([
        'class_session_id' => $sesi->id,
        'student_id' => $siswa->id,
        'status' => 'hadir',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('1 Siswa Terdaftar', false);
});

/**
 * Buat satu kelas uji beserta periode akademiknya.
 */
function kelasUntukTesting(string $code = '12-IPA-1'): ClassGroup
{
    $periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    return ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => $code,
        'status' => 'aktif',
    ]);
}

/**
 * Buat akun tentor beserta profil stafnya.
 *
 * @return array{0: User, 1: StaffProfile}
 */
function tentorDenganProfil(): array
{
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $staff = StaffProfile::create([
        'user_id' => $user->id,
        'employee_number' => 'T009',
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    return [$user, $staff];
}
