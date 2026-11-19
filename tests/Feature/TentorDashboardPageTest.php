<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\QualifixTicket;
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
        ->assertSee('id="liveClock"', false);
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

test('dashboard, sidebar, dan topbar tentor menampilkan nama tentor dari staff profile', function () {
    $user = User::factory()->create([
        'role' => 'tentor',
        'status' => 'aktif',
        'name' => 'Tentor Matematika',
    ]);

    StaffProfile::create([
        'user_id' => $user->id,
        'employee_number' => 'TTR-001',
        'full_name_with_title' => 'Kak Dimas Wahyu, S.Pd',
        'job_title' => 'Master Tentor Matematika & Saintek',
        'status' => 'aktif',
    ]);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang, Kak Dimas Wahyu, S.Pd! 👋', false)
        ->assertDontSee('Selamat datang, Tentor Matematika!', false)
        ->assertSee('Kak Dimas Wahyu, S.Pd', false)
        ->assertDontSee('Farhan Kamil', false)
        ->assertDontSee('Kak Hendra', false);
});

test('stat card menampilkan tulisan Qualifix bukan Klinik PR Siswa', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $this->actingAs($user)
        ->get(route('tentor.dashboard'))
        ->assertOk()
        ->assertSee('Qualifix')
        ->assertDontSee('Klinik PR Siswa');
});

test('qualifix siswa di dashboard hanya menampilkan maksimal 2 pertanyaan dan sisanya dapat diakses melalui tautan', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $staff = StaffProfile::create([
        'user_id' => $user->id,
        'employee_number' => 'TTR-002',
        'full_name_with_title' => 'Kak Maya S.Si',
        'job_title' => 'Tentor Fisika',
        'status' => 'aktif',
    ]);

    $period = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $kelas = ClassGroup::create([
        'academic_period_id' => $period->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
    ]);

    $siswaUser = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $student = Student::create([
        'user_id' => $siswaUser->id,
        'class_group_id' => $kelas->id,
        'registration_number' => 'REG-123',
        'nisn' => '1234567890',
        'full_name' => 'Ahmad Siswa',
        'status' => 'aktif',
    ]);

    $subject = Subject::create(['code' => 'FIS', 'name' => 'Fisika']);

    // Buat 3 tiket menunggu tanggapan untuk tentor ini
    for ($i = 1; $i <= 3; $i++) {
        $ticket = QualifixTicket::create([
            'ticket_number' => "QX-2026-00{$i}",
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'title' => "Pertanyaan Fisika Seri {$i}",
            'question' => "Isi detail pertanyaan fisika nomor {$i}",
            'status' => 'menunggu_tentor',
            'submitted_at' => now()->subMinutes($i * 10),
        ]);
        $ticket->staff()->attach($staff->id);
    }

    $response = $this->actingAs($user)->get(route('tentor.dashboard'));
    $response->assertOk();

    // Hanya 2 tiket pertama yang tampil
    $response->assertSee('Pertanyaan Fisika Seri 1');
    $response->assertSee('Pertanyaan Fisika Seri 2');
    $response->assertDontSee('Pertanyaan Fisika Seri 3');

    // Terdapat teks tautan untuk melihat 1 pertanyaan lainnya
    $response->assertSee('Lihat 1 pertanyaan lainnya di Qualifix');
});

test('dashboard tentor hanya menampilkan sesi miliknya sendiri', function () {
    [$user, $staff] = tentorDenganProfilTesting();

    $kelas = kelasUntukTestingDashboard();
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
    [$user, $staff] = tentorDenganProfilTesting();
    $kelas = kelasUntukTestingDashboard();
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
    [$user, $staff] = tentorDenganProfilTesting();
    $kelas = kelasUntukTestingDashboard();
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

function kelasUntukTestingDashboard(string $code = '12-IPA-1'): ClassGroup
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
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
    ]);
}

function tentorDenganProfilTesting(): array
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
