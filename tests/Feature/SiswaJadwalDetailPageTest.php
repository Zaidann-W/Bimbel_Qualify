<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Room;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudySession;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Waktu dibekukan supaya pengelompokan "hari ini" tidak
    // bergantung pada jam serta tanggal saat test dijalankan.
    Carbon::setTestNow(Carbon::parse('2026-11-12 16:00:00'));

    $this->actingAs(User::factory()->create(['role' => 'siswa']));

    StudySession::factory()->create([
        'title' => 'Kalkulus & Integral Lanjutan',
        'category_badge' => 'Matematika Saintek',
        'date' => today(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'room_name' => 'Ruang Eureka (Lantai 2)',
        'room_capacity' => 15,
        'tutor_name' => 'Kak Hendra Wijaya, M.Sc',
    ]);

    StudySession::factory()->create([
        'title' => 'Termokimia & Kesetimbangan Kimia',
        'category_badge' => 'Kimia Saintek',
        'date' => today()->addDays(3),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'room_name' => 'Ruang Volta (Lantai 2)',
        'tutor_name' => 'Dr Hartanto, M.Pd',
    ]);

    StudySession::factory()->create([
        'title' => 'Fisika Modern & Mekanika Kuantum',
        'category_badge' => 'Fisika Saintek',
        'date' => today()->subDays(2),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'room_name' => 'Ruangan Newton (Lantai 1)',
        'tutor_name' => 'Kak Salsabila Rahma, S.Pd',
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

test('halaman jadwal siswa mengembalikan respons sukses', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200);
    $response->assertSee('Jadwal Belajar Siswa');
    $response->assertSee('Kalkulus &amp; Integral Lanjutan', false);
    $response->assertSee('Termokimia &amp; Kesetimbangan Kimia', false);
    $response->assertSee('Fisika Modern &amp; Mekanika Kuantum', false);
    $response->assertSee('Kak Hendra Wijaya');
});

test('halaman jadwal siswa dapat diakses lewat rutenya yang bernama', function () {
    $this->get(route('siswa.jadwal.detail'))->assertStatus(200);
});

test('sesi dikelompokkan menjadi hari ini, selanjutnya, dan sebelumnya', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200)
        ->assertSee('Hari Ini')
        ->assertSee('Selanjutnya')
        ->assertSee('Sebelumnya');
});

test('setiap sesi menampilkan waktu, tentor, dan ruangan', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200)
        ->assertSee('15:30 – 17:00')
        ->assertSee('Kak Hendra Wijaya')
        ->assertSee('Ruang Eureka, Lt. 2')
        ->assertSee('Ruang Volta, Lt. 2');
});

test('seluruh sesi tertaut ke halaman detail sesi', function () {
    $response = $this->get('/siswa/jadwal/detail');

    StudySession::query()->get()->each(function (StudySession $session) use ($response) {
        $response->assertSee(route('siswa.jadwal.show', $session), false);
    });
});

test('header menampilkan semester dan kelas siswa yang sedang aktif', function () {
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
        'user_id' => auth()->id(),
        'full_name' => 'Aisyah Putri Azzahra',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Semester Ganjil 2026/2027')
        ->assertSee('Kelas 12 IPA');
});

test('sesi hari ini tidak ikut masuk ke bagian sebelumnya', function () {
    $html = $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->getContent();

    expect(substr_count($html, 'Kalkulus &amp; Integral Lanjutan'))->toBe(1)
        ->and(substr_count($html, 'Fisika Modern &amp; Mekanika Kuantum'))->toBe(1);
});

test('kartu statistik dan filter lama tidak lagi ditampilkan', function () {
    $response = $this->get('/siswa/jadwal/detail');

    $response->assertStatus(200)
        ->assertDontSee('Sesi Berjalan Hari Ini')
        ->assertDontSee('Total Sesi Pekan Ini')
        ->assertDontSee('Semua Matpel');
});

test('halaman jadwal siswa menampilkan empty state tanpa data', function () {
    StudySession::query()->delete();

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Belum ada sesi belajar yang terjadwal.');
});

test('jadwal kelas siswa ikut tampil di halaman jadwal saya', function () {
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
        'user_id' => auth()->id(),
        'full_name' => 'Aisyah Putri Azzahra',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $ruang = Room::create(['name' => 'Ruang Eureka', 'floor' => 'Lt. 2', 'status' => 'aktif']);

    $tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $staff = StaffProfile::create([
        'user_id' => $tentor->id,
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    ClassSession::create([
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

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Jadwal Kelas')
        ->assertSee('Matematika Saintek')
        ->assertSee('Integral Substitusi &amp; Parsial', false)
        ->assertSee('Kak Hendra Wijaya');
});

test('hanya jadwal kelas aktif siswa yang ditampilkan', function () {
    $periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $kelasIkut = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $kelasTidakIkut = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '11-IPS-1',
        'education_level' => 'SMA',
        'grade' => '11',
        'major' => 'IPS',
        'status' => 'aktif',
    ]);

    $siswa = Student::create([
        'user_id' => auth()->id(),
        'full_name' => 'Aisyah Putri Azzahra',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelasIkut->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $staff = StaffProfile::create([
        'user_id' => $tentor->id,
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $mapelLain = Subject::create(['code' => 'SEJ', 'name' => 'Sejarah Indonesia']);

    ClassSession::create([
        'class_group_id' => $kelasIkut->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'session_date' => today()->addDays(1)->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'topic' => 'Integral',
        'status' => 'terjadwal',
    ]);

    ClassSession::create([
        'class_group_id' => $kelasTidakIkut->id,
        'subject_id' => $mapelLain->id,
        'staff_id' => $staff->id,
        'session_date' => today()->addDays(1)->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'topic' => 'Kolonialisme',
        'status' => 'terjadwal',
    ]);

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Matematika Saintek')
        ->assertDontSee('Sejarah Indonesia');
});

test('sesi jadwal kelas dibedakan menjadi hari ini, selanjutnya, dan sebelumnya', function () {
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
        'user_id' => auth()->id(),
        'full_name' => 'Aisyah Putri Azzahra',
        'status' => 'aktif',
    ]);

    Enrollment::create([
        'student_id' => $siswa->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $staff = StaffProfile::create([
        'user_id' => $tentor->id,
        'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
        'job_title' => 'Master Tentor',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    // Sesi hari ini, mendatang, dan lampau.
    foreach ([-1, 0, 3] as $offset) {
        ClassSession::create([
            'class_group_id' => $kelas->id,
            'subject_id' => $mapel->id,
            'staff_id' => $staff->id,
            'session_date' => today()->addDays($offset)->toDateString(),
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'topic' => 'Topik '.$offset,
            'status' => 'terjadwal',
        ]);
    }

    $this->get('/siswa/jadwal/detail')
        ->assertStatus(200)
        ->assertSee('Jadwal Kelas')
        ->assertSee('Hari Ini')
        ->assertSee('Selanjutnya')
        ->assertSee('Sebelumnya');
});
