<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));

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
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->subjectId = DB::table('subjects')->insertGetId([
        'code' => 'MTK',
        'name' => 'Matematika',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tentorUserId = DB::table('users')->insertGetId([
        'name' => 'Dimas Wahyu',
        'email' => 'dimas@bimbel.test',
        'password' => bcrypt('rahasia'),
        'role' => 'tentor',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->staffId = DB::table('staff_profiles')->insertGetId([
        'user_id' => $tentorUserId,
        'employee_number' => 'TTR-001',
        'full_name_with_title' => 'Dimas Wahyu, S.Pd',
        'job_title' => 'Tentor Matematika',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->roomId = DB::table('rooms')->insertGetId([
        'name' => 'R. Bima',
        'floor' => 'Lt. 2',
        'capacity' => 30,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->shiftId = DB::table('study_shifts')->insertGetId([
        'name' => 'Sesi Sore',
        'starts_at' => '15:30:00',
        'ends_at' => '17:30:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->sessionId = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => '2026-10-08',
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->tanggal = '2026-10-08';
});

test('halaman absensi dan jadwal menampilkan bagian utama', function () {
    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Manajemen Jadwal &amp; Presensi Bimbel', false)
        ->assertSee('Total Sesi 08/10/2026')
        ->assertSee('Presensi Siswa')
        ->assertSee('Kehadiran Tentor')
        ->assertSee('Pengajuan Izin / Sakit')
        ->assertSee('Verifikasi Izin &amp; Sakit', false)
        ->assertSee('Ketersediaan Ruangan')
        ->assertSee('Kepadatan Jadwal Pekan Ini')
        ->assertSee('Kelola Jadwal');
});

test('kartu ringkasan memakai label hari ini saat tanggal aktif adalah hari ini', function () {
    $this->get(route('admin.absensiJadwal'))
        ->assertOk()
        ->assertSee('Total Sesi Hari Ini');
});

test('halaman memberi tahu sesi berikutnya saat tanggal aktif tidak punya sesi', function () {
    DB::table('class_sessions')->where('id', $this->sessionId)->update([
        'session_date' => today()->addDays(3)->toDateString(),
    ]);

    $this->get(route('admin.absensiJadwal'))
        ->assertOk()
        ->assertSee('Sesi Berikutnya')
        ->assertSee('Belum ada sesi terjadwal pada')
        ->assertSee('Sesi berikutnya:')
        ->assertSee('Lihat Sesi');
});

test('jumlah sesi pada kartu ringkasan mengikuti data di database', function () {
    $shiftMalamId = DB::table('study_shifts')->insertGetId([
        'name' => 'Sesi Malam',
        'starts_at' => '18:30:00',
        'ends_at' => '20:30:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('class_sessions')->insert([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $shiftMalamId,
        'session_date' => '2026-10-08',
        'start_time' => '18:30:00',
        'end_time' => '20:00:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Sesi Sore: 1')
        ->assertSee('Sesi Malam: 1');
});

test('tabel jadwal menampilkan sesi pada tanggal yang dipilih', function () {
    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Matematika')
        ->assertSee('Dimas Wahyu, S.Pd')
        ->assertSee('12-IPA-1')
        ->assertSee('R. Bima')
        ->assertSee('15:30 - 17:30');
});

test('sesi pada tanggal lain tidak tampil pada tanggal yang dipilih', function () {
    DB::table('class_sessions')->insert([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => '2026-10-15',
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('1 Kelas Terjadwal');
});

test('sesi yang dibatalkan tidak dihitung pada kartu ringkasan', function () {
    DB::table('class_sessions')->where('id', $this->sessionId)->update(['status' => 'dibatalkan']);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Sesi Sore: 0')
        ->assertSee('Dibatalkan');
});

test('tingkat kehadiran siswa dihitung dari presensi pada tanggal yang dipilih', function () {
    $studentIds = collect(['Aisyah Nurul', 'Bagas Pratama'])->map(fn (string $nama) => DB::table('students')->insertGetId([
        'full_name' => $nama,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]));

    DB::table('student_attendance')->insert([
        [
            'class_session_id' => $this->sessionId,
            'student_id' => $studentIds[0],
            'status' => 'hadir',
            'arrived_at' => '2026-10-08 15:15:00',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'class_session_id' => $this->sessionId,
            'student_id' => $studentIds[1],
            'status' => 'alpa',
            'arrived_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('50%')
        ->assertSee('1 Hadir')
        ->assertSee('1 Tidak Hadir');
});

test('kehadiran tentor menampilkan catatan saat belum ada data check-in', function () {
    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Belum ada data check-in tentor');
});

test('check-in tentor ditampilkan pada baris sesi', function () {
    DB::table('tutor_attendance')->insert([
        'class_session_id' => $this->sessionId,
        'staff_id' => $this->staffId,
        'check_in_at' => '2026-10-08 15:15:00',
        'status' => 'hadir',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Check-in 15:15');
});

test('ketersediaan ruangan mengikuti sesi pada tanggal yang dipilih', function () {
    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('R. Bima')
        ->assertSee('Sesi Sore Aktif');
});

test('pengajuan izin yang menunggu ditampilkan di panel verifikasi', function () {
    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aura Clarissa Putri',
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

    DB::table('leave_requests')->insert([
        'student_id' => $studentId,
        'class_session_id' => $this->sessionId,
        'type' => 'sakit',
        'reason' => 'Demam',
        'status' => 'menunggu',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Aura Clarissa Putri')
        ->assertSee('Sakit')
        ->assertSee('Demam')
        ->assertSee('1 Menunggu');
});

test('card presensi menampilkan jumlah siswa yang belum absen', function () {
    $studentIds = collect(['Aisyah Nurul', 'Bagas Pratama'])->map(fn (string $nama) => DB::table('students')->insertGetId([
        'full_name' => $nama,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]));

    DB::table('enrollments')->insert([
        [
            'student_id' => $studentIds[0],
            'class_group_id' => $this->classId,
            'started_on' => '2026-07-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'student_id' => $studentIds[1],
            'class_group_id' => $this->classId,
            'started_on' => '2026-07-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('0 / 2 siswa belum absen');
});

test('card presensi memberi tahu saat rombel belum punya siswa enrolled', function () {
    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('Belum ada siswa enrolled di kelas ini');
});

test('card kehadiran tentor terisi setelah tentor check-in', function () {
    DB::table('tutor_attendance')->insert([
        'class_session_id' => $this->sessionId,
        'staff_id' => $this->staffId,
        'check_in_at' => '2026-10-08 15:15:00',
        'status' => 'hadir',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.absensiJadwal', ['tanggal' => $this->tanggal]))
        ->assertOk()
        ->assertSee('1 / 1')
        ->assertSee('100% Mengajar')
        ->assertSee('Tepat Waktu');
});

test('halaman absensi dan jadwal tidak bisa dibuka oleh selain admin', function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $this->get(route('admin.absensiJadwal'))->assertRedirect(route('tentor.dashboard'));
});
