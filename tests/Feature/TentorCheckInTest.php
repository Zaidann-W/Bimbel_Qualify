<?php

use App\Models\TutorAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $periodId = DB::table('academic_periods')->insertGetId([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->classId = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $periodId,
        'code' => '12-IPA-1',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->staffId = DB::table('staff_profiles')->insertGetId([
        'user_id' => auth()->id(),
        'employee_number' => 'TTR-001',
        'full_name_with_title' => 'Dimas Wahyu, S.Pd',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->sessionId = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $this->classId,
        'subject_id' => DB::table('subjects')->insertGetId([
            'code' => 'MTK',
            'name' => 'Matematika',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'staff_id' => $this->staffId,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

test('tentor bisa check-in pada sesinya', function () {
    Carbon::setTestNow(Carbon::parse(today()->toDateString().' 15:00:00'));

    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId])
        ->assertRedirect();

    $this->assertDatabaseHas('tutor_attendance', [
        'class_session_id' => $this->sessionId,
        'staff_id' => $this->staffId,
        'status' => 'hadir',
    ]);
});

test('check-in lewat 15 menit tercatat sebagai terlambat', function () {
    Carbon::setTestNow(Carbon::parse(today()->toDateString().' 16:00:00'));

    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId]);

    $this->assertDatabaseHas('tutor_attendance', [
        'class_session_id' => $this->sessionId,
        'staff_id' => $this->staffId,
        'status' => 'terlambat',
    ]);
});

test('check-in pada sesi milik tentor lain ditolak', function () {
    $staffLain = DB::table('staff_profiles')->insertGetId([
        'user_id' => User::factory()->create(['role' => 'tentor', 'status' => 'aktif'])->id,
        'employee_number' => 'TTR-002',
        'full_name_with_title' => 'Rina Novianti, M.Si',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('class_sessions')->where('id', $this->sessionId)->update(['staff_id' => $staffLain]);

    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId])
        ->assertSessionHasErrors('error');

    $this->assertDatabaseCount('tutor_attendance', 0);
});

test('check-in kedua pada sesi yang sama tidak menggandakan data', function () {
    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId]);
    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId]);

    $this->assertDatabaseCount('tutor_attendance', 1);
});

test('tentor bisa checkout setelah check-in', function () {
    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId]);
    $this->post(route('tentor.absensi.check-out'), ['session_id' => $this->sessionId]);

    $kehadiran = TutorAttendance::where('class_session_id', $this->sessionId)->first();

    expect($kehadiran)->not->toBeNull()
        ->and($kehadiran->check_in_at)->not->toBeNull()
        ->and($kehadiran->check_out_at)->not->toBeNull();
});

test('halaman absensi menampilkan tombol check-in saat belum check-in', function () {
    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('Kehadiran Anda')
        ->assertSee('Belum check-in')
        ->assertSee('Check-in');
});

test('halaman absensi menampilkan jam check-in setelah(check-in)', function () {
    $this->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId]);

    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('Sudah check-in pukul')
        ->assertSee('Checkout');
});

test('session check-in harus ada', function () {
    $this->post(route('tentor.absensi.check-in'), ['session_id' => 99999])
        ->assertSessionHasErrors('session_id');
});

test('tentor tanpa profil staf tidak bisa check-in', function () {
    $tanpaProfil = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $this->actingAs($tanpaProfil)
        ->post(route('tentor.absensi.check-in'), ['session_id' => $this->sessionId])
        ->assertSessionHasErrors('error');

    $this->assertDatabaseCount('tutor_attendance', 0);
});

test('sesi hari ini milik tentor lain tidak muncul di daftar sesi', function () {
    $staffLain = DB::table('staff_profiles')->insertGetId([
        'user_id' => User::factory()->create(['role' => 'tentor', 'status' => 'aktif'])->id,
        'employee_number' => 'TTR-003',
        'full_name_with_title' => 'Rina Novianti, M.Si',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('class_sessions')->where('id', $this->sessionId)->update(['staff_id' => $staffLain]);

    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('Tidak Ada Sesi');
});

test('sesi milik tentor tampil dengan roster siswa enrolled', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aisyah Nurul',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('Aisyah Nurul')
        ->assertSee('Total Siswa');
});

test('sesi pada tanggal lain bisa dibuka lewat parameter session_id', function () {
    $sessionLain = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $this->classId,
        'subject_id' => DB::table('subjects')->insertGetId([
            'code' => 'FIS',
            'name' => 'Fisika',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'staff_id' => $this->staffId,
        'session_date' => today()->addDays(3)->toDateString(),
        'start_time' => '18:30:00',
        'end_time' => '20:00:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $sessionLain]))
        ->assertOk()
        ->assertSee('Fisika')
        ->assertSee('Sesi '.today()->addDays(3)->locale('id')->translatedFormat('d/m/Y'));
});

test('sesi milik tentor lain tidak bisa dibuka lewat parameter session_id', function () {
    $staffLain = DB::table('staff_profiles')->insertGetId([
        'user_id' => User::factory()->create(['role' => 'tentor', 'status' => 'aktif'])->id,
        'employee_number' => 'TTR-004',
        'full_name_with_title' => 'Rina Novianti, M.Si',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sessionLain = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $this->classId,
        'subject_id' => DB::table('subjects')->insertGetId([
            'code' => 'BIO',
            'name' => 'Biologi',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'staff_id' => $staffLain,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $sessionLain]))
        ->assertOk()
        ->assertDontSee('Biologi');
});

test('rentang tanggal dari query membatasi daftar sesi', function () {
    $sebelum = today()->subDays(3)->toDateString();

    DB::table('class_sessions')->insert([
        'class_group_id' => $this->classId,
        'subject_id' => DB::table('subjects')->insertGetId([
            'code' => 'KIM',
            'name' => 'Kimia',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'staff_id' => $this->staffId,
        'session_date' => $sebelum,
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('2 Sesi');

    // Dengan rentang hanya hari ini, sesi 3 hari lalu tidak masuk daftar.
    $this->get(route('tentor.absensi', ['dari' => today()->toDateString(), 'sampai' => today()->toDateString()]))
        ->assertOk()
        ->assertSee('1 Sesi');
});

test('sesi lampau tidak menampilkan tombol check-in', function () {
    DB::table('class_sessions')->where('id', $this->sessionId)->update([
        'session_date' => today()->subDay()->toDateString(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $this->sessionId]))
        ->assertOk()
        ->assertSee('Tidak Check-in')
        ->assertSee('Bisa check-in pada hari sesi')
        ->assertDontSee('>Check-in<');
});

test('sesi akan datang menampilkan tombol check-in yang tidak aktif', function () {
    DB::table('class_sessions')->where('id', $this->sessionId)->update([
        'session_date' => today()->addDay()->toDateString(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $this->sessionId]))
        ->assertOk()
        ->assertSee('Akan datang')
        ->assertSee('Bisa check-in pada hari sesi');
});

test('empty state menawarkan tautan lihat 30 hari ke depan', function () {
    $this->get(route('tentor.absensi', ['dari' => today()->addYear()->toDateString(), 'sampai' => today()->addYear()->addDays(5)->toDateString()]))
        ->assertOk()
        ->assertSee('Tidak ada sesi mengajar pada rentang ini')
        ->assertSee('Lihat 30 hari ke depan');
});

test('sesi yang dibatalkan tidak muncul di daftar sesi', function () {
    DB::table('class_sessions')->where('id', $this->sessionId)->update(['status' => 'dibatalkan']);

    $this->get(route('tentor.absensi'))
        ->assertOk()
        ->assertSee('Tidak Ada Sesi');
});

test('tentor dapat memasukkan status kehadiran siswa', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Budi Santoso',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->post(route('tentor.absensi.store'), [
        'session_id' => $this->sessionId,
        'attendances' => [
            $studentId => [
                'student_id' => $studentId,
                'status' => 'hadir',
                'arrived_at' => '15:35',
            ],
        ],
    ]);

    $response->assertRedirect(route('tentor.absensi', ['session_id' => $this->sessionId]));
    $response->assertSessionHas('success', 'Absensi berhasil disimpan.');

    $this->assertDatabaseHas('student_attendance', [
        'class_session_id' => $this->sessionId,
        'student_id' => $studentId,
        'status' => 'hadir',
    ]);
});

test('sesi yang belum disimpan absensinya menampilkan status belum disimpan', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Citra Lestari',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $this->sessionId]))
        ->assertOk()
        ->assertSee('Absensi Belum Disimpan')
        ->assertSee('Simpan Presensi Kelas')
        ->assertDontSee('Absensi Telah Tersimpan');
});

test('sesi yang telah disimpan absensinya dapat diedit saat jam pembelajaran berlangsung', function () {
    Carbon::setTestNow(Carbon::parse(today()->toDateString().' 16:00:00'));

    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Dewi Anggraini',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('student_attendance')->insert([
        'class_session_id' => $this->sessionId,
        'student_id' => $studentId,
        'status' => 'hadir',
        'recorded_by' => auth()->id(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $this->sessionId]))
        ->assertOk()
        ->assertSee('Absensi Telah Tersimpan')
        ->assertSee('Edit Absensi')
        ->assertSee('Edit Presensi');

    // Coba edit status menjadi izin saat jam sesi
    $response = $this->post(route('tentor.absensi.store'), [
        'session_id' => $this->sessionId,
        'attendances' => [
            $studentId => [
                'student_id' => $studentId,
                'status' => 'izin',
                'note' => 'Izin keperluan keluarga',
            ],
        ],
    ]);

    $response->assertRedirect(route('tentor.absensi', ['session_id' => $this->sessionId]));
    $response->assertSessionHas('success', 'Absensi berhasil disimpan.');

    $this->assertDatabaseHas('student_attendance', [
        'class_session_id' => $this->sessionId,
        'student_id' => $studentId,
        'status' => 'izin',
    ]);
});

test('sesi yang telah disimpan absensinya tidak dapat diedit di luar jam pembelajaran', function () {
    // Sesi 15:30 - 17:30, uji coba waktu pukul 18:00 (sudah lewat jam pembelajaran)
    Carbon::setTestNow(Carbon::parse(today()->toDateString().' 18:00:00'));

    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Fajar Pratama',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $this->classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('student_attendance')->insert([
        'class_session_id' => $this->sessionId,
        'student_id' => $studentId,
        'status' => 'hadir',
        'recorded_by' => auth()->id(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('tentor.absensi', ['session_id' => $this->sessionId]))
        ->assertOk()
        ->assertSee('Edit Hanya Saat Jam Pembelajaran')
        ->assertSee('Terkunci di Luar Jam Sesi');

    // Coba kirim request edit absensi di luar jam pembelajaran
    $response = $this->post(route('tentor.absensi.store'), [
        'session_id' => $this->sessionId,
        'attendances' => [
            $studentId => [
                'student_id' => $studentId,
                'status' => 'alpa',
            ],
        ],
    ]);

    $response->assertSessionHasErrors('error');

    // Data di database tetap 'hadir' (tidak berubah)
    $this->assertDatabaseHas('student_attendance', [
        'class_session_id' => $this->sessionId,
        'student_id' => $studentId,
        'status' => 'hadir',
    ]);
});

test('dashboard tentor menyediakan tautan langsung ke absensi sesi hari ini', function () {
    $response = $this->get(route('tentor.dashboard'));
    $response->assertOk();
    $response->assertSee(route('tentor.absensi', ['session_id' => $this->sessionId]));
    $response->assertSee('Input Absen Kelas');
});
