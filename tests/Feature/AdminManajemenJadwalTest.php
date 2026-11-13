<?php

use App\Models\ClassSession;
use App\Models\Room;
use App\Models\ScheduleRule;
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

    $this->ruleId = DB::table('schedule_rules')->insertGetId([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'weekday' => 4, // Kamis
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('halaman manajemen jadwal menampilkan aturan dan sesi terjadwal', function () {
    $this->get(route('admin.manajemenJadwal', ['tab' => 'aturan']))
        ->assertOk()
        ->assertSee('Manajemen Jadwal Sesi')
        ->assertSee('Aturan Jadwal Mingguan')
        ->assertSee('12-IPA-1')
        ->assertSee('Dimas Wahyu, S.Pd')
        ->assertSee('Kamis')
        ->assertSee('R. Bima');
});

test('halaman manajemen jadwal menampilkan sesi pada rentang tanggal terpilih', function () {
    DB::table('class_sessions')->insert([
        'schedule_rule_id' => $this->ruleId,
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

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi', 'dari' => '2026-10-01', 'sampai' => '2026-10-31']))
        ->assertOk()
        ->assertSee('Sesi Terjadwal')
        ->assertSee('Matematika')
        ->assertSee('Terjadwal')
        ->assertSee('Kamis, 08 Okt 2026');
});

test('admin bisa membuat aturan jadwal mingguan', function () {
    $this->post(route('admin.manajemenJadwal.aturan.store'), [
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'weekday' => 5,
        'start_time' => '18:30',
        'end_time' => '20:00',
    ])->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'aturan']));

    $this->assertDatabaseHas('schedule_rules', [
        'class_group_id' => $this->classId,
        'weekday' => 5,
        'start_time' => '18:30:00',
        'end_time' => '20:00:00',
    ]);
});

test('aturan jadwal dengan jam selesai mendahului jam mulai ditolak', function () {
    $this->post(route('admin.manajemenJadwal.aturan.store'), [
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'weekday' => 5,
        'start_time' => '18:30',
        'end_time' => '17:30',
    ])->assertSessionHasErrors('end_time');

    $this->assertDatabaseCount('schedule_rules', 1);
});

test('generate membuat sesi pada tanggal yang cocok dengan aturan', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ])->assertRedirect(route('admin.manajemenJadwal', [
        'tab' => 'sesi',
        'dari' => '2026-10-01',
        'sampai' => '2026-10-31',
    ]));

    // Oktober 2026 punya lima Kamis: 1, 8, 15, 22, dan 29.
    $this->assertDatabaseCount('class_sessions', 5);
    $this->assertDatabaseHas('class_sessions', [
        'class_group_id' => $this->classId,
        'session_date' => '2026-10-08',
        'start_time' => '15:30:00',
        'status' => 'terjadwal',
    ]);
});

test('generate tidak menggandakan sesi yang sudah ada', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ]);

    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ]);

    $this->assertDatabaseCount('class_sessions', 5);
});

test('generate melewati aturan yang bentrok dengan sesi lain di jam sama', function () {
    DB::table('schedule_rules')->insert([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'weekday' => 4,
        'start_time' => '16:00:00',
        'end_time' => '18:00:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ]);

    // Aturan pertama tetap dibuat, aturan kedua bentrok jam sehingga dilewati.
    $this->assertDatabaseCount('class_sessions', 5);
});

test('generate hanya membuat sesi untuk rombel yang dipilih', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
        'class_group_ids' => [$this->classId],
    ]);

    $this->assertDatabaseCount('class_sessions', 5);

    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
        'class_group_ids' => [],
    ]);

    $this->assertDatabaseCount('class_sessions', 5);
});

test('admin bisa mengubah sesi terjadwal', function () {
    $sessionId = DB::table('class_sessions')->insertGetId([
        'schedule_rule_id' => $this->ruleId,
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => '2026-10-08',
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'topic' => 'Persamaan Kuadrat',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->put(route('admin.manajemenJadwal.sesi.update', $sessionId), [
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => '2026-10-09',
        'start_time' => '18:30',
        'end_time' => '20:00',
        'topic' => 'Fungsi Kuadrat',
        'status' => 'berlangsung',
    ])->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'sesi']));

    $session = ClassSession::findOrFail($sessionId);

    expect($session->tanggal->toDateString())->toBe('2026-10-09')
        ->and($session->start_time)->toBe('18:30:00')
        ->and($session->status)->toBe('berlangsung')
        ->and($session->topic)->toBe('Fungsi Kuadrat');
});

test('admin bisa membatalkan sesi terjadwal', function () {
    $sessionId = DB::table('class_sessions')->insertGetId([
        'schedule_rule_id' => $this->ruleId,
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

    $this->post(route('admin.manajemenJadwal.sesi.batalkan', $sessionId))
        ->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'sesi']));

    expect(ClassSession::findOrFail($sessionId)->status)->toBe('dibatalkan');
});

test('menghapus aturan jadwal mempertahankan sesi lampau beserta presensinya', function () {
    DB::table('class_sessions')->insert([
        'schedule_rule_id' => $this->ruleId,
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

    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aisyah Nurul',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('student_attendance')->insert([
        'class_session_id' => DB::table('class_sessions')->where('schedule_rule_id', $this->ruleId)->value('id'),
        'student_id' => $studentId,
        'status' => 'hadir',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->delete(route('admin.manajemenJadwal.aturan.destroy', $this->ruleId))
        ->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'aturan']));

    $this->assertDatabaseMissing('schedule_rules', ['id' => $this->ruleId]);
    $this->assertDatabaseCount('class_sessions', 1);
    $this->assertDatabaseCount('student_attendance', 1);
});

test('menghapus aturan jadwal menghapus sesi yang akan datang', function () {
    DB::table('class_sessions')->insert([
        'schedule_rule_id' => $this->ruleId,
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => today()->addDay()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->delete(route('admin.manajemenJadwal.aturan.destroy', $this->ruleId))
        ->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'aturan']));

    $this->assertDatabaseMissing('schedule_rules', ['id' => $this->ruleId]);
    $this->assertDatabaseCount('class_sessions', 0);
});

test('sesi lampau menampilkan status selesai tanpa mengubah kolom status', function () {
    DB::table('class_sessions')->insert([
        'schedule_rule_id' => $this->ruleId,
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => today()->subDay()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi', 'dari' => today()->startOfMonth()->toDateString(), 'sampai' => today()->endOfMonth()->toDateString()]))
        ->assertOk()
        ->assertSee('Selesai');

    $this->assertDatabaseHas('class_sessions', ['status' => 'terjadwal']);
});

test('filter status menyaring sesi lampau ke status selesai', function () {
    DB::table('class_sessions')->insert([
        'schedule_rule_id' => $this->ruleId,
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => today()->subDay()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $dari = today()->startOfMonth()->toDateString();
    $sampai = today()->endOfMonth()->toDateString();

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi', 'dari' => $dari, 'sampai' => $sampai, 'status' => 'selesai']))
        ->assertOk()
        ->assertSee('1 Sesi');

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi', 'dari' => $dari, 'sampai' => $sampai, 'status' => 'terjadwal']))
        ->assertOk()
        ->assertSee('0 Sesi');
});

test('generate ditolak kalau rentang tanggal melebihi 92 hari', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2027-01-15',
    ])->assertSessionHasErrors('to_date');

    $this->assertDatabaseCount('class_sessions', 0);
});

test('simulasi menampilkan estimasi tanpa membuat sesi', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
        'preview' => '1',
    ])->assertRedirect(route('admin.manajemenJadwal', [
        'tab' => 'sesi',
        'dari' => '2026-10-01',
        'sampai' => '2026-10-31',
    ]));

    $this->assertDatabaseCount('class_sessions', 0);

    $this->followingRedirects()
        ->post(route('admin.manajemenJadwal.sesi.generate'), [
            'from_date' => '2026-10-01',
            'to_date' => '2026-10-31',
            'preview' => '1',
        ])
        ->assertOk()
        ->assertSee('Simulasi: 5 sesi akan dibuat dari aturan jadwal.')
        ->assertSee('Belum ada sesi yang disimpan.');
});

test('halaman manajemen jadwal tidak bisa dibuka oleh selain admin', function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $this->get(route('admin.manajemenJadwal'))->assertRedirect(route('tentor.dashboard'));
});

test('formulir aturan dan sesi dapat dibuka', function () {
    $this->get(route('admin.manajemenJadwal.aturan.create'))
        ->assertOk()
        ->assertSee('Tambah Aturan Jadwal Mingguan')
        ->assertSee('Matematika')
        ->assertSee('Dimas Wahyu, S.Pd');

    $this->get(route('admin.manajemenJadwal.aturan.edit', $this->ruleId))
        ->assertOk()
        ->assertSee('Edit Aturan Jadwal')
        ->assertSee('12-IPA-1');
});

test('aturan jadwal dengan hari di luar rentang-weekday ditolak', function () {
    $rule = ScheduleRule::findOrFail($this->ruleId);

    $this->put(route('admin.manajemenJadwal.aturan.update', $rule), [
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'weekday' => 9,
        'start_time' => '15:30',
        'end_time' => '17:30',
    ])->assertSessionHasErrors('weekday');
});

test('admin bisa menambah ruang belajar', function () {
    $this->post(route('admin.manajemenJadwal.ruang.store'), [
        'name' => 'R. Gajah Mada',
        'floor' => 'Lt. 1',
        'capacity' => 30,
        'status' => 'aktif',
    ])->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'ruang']));

    $this->assertDatabaseHas('rooms', [
        'name' => 'R. Gajah Mada',
        'floor' => 'Lt. 1',
        'capacity' => 30,
        'status' => 'aktif',
    ]);
});

test('nama ruang belajar harus unik', function () {
    $this->post(route('admin.manajemenJadwal.ruang.store'), [
        'name' => 'R. Bima',
        'status' => 'aktif',
    ])->assertSessionHasErrors('name');

    $this->assertDatabaseCount('rooms', 1);
});

test('admin bisa mengubah ruang belajar', function () {
    $room = Room::findOrFail($this->roomId);

    $this->put(route('admin.manajemenJadwal.ruang.update', $room), [
        'name' => 'R. Bima Lantai 3',
        'floor' => 'Lt. 3',
        'capacity' => 40,
        'status' => 'nonaktif',
    ])->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'ruang']));

    $room->refresh();

    expect($room->name)->toBe('R. Bima Lantai 3')
        ->and($room->floor)->toBe('Lt. 3')
        ->and($room->capacity)->toBe(40)
        ->and($room->status)->toBe('nonaktif');
});

test('ruang yang dipakai sesi terjadwal tidak bisa dihapus', function () {
    DB::table('class_sessions')->insert([
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

    $this->from(route('admin.manajemenJadwal', ['tab' => 'ruang']))
        ->delete(route('admin.manajemenJadwal.ruang.destroy', $this->roomId))
        ->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'ruang']))
        ->assertSessionHasErrors('ruang');

    $this->assertDatabaseHas('rooms', ['id' => $this->roomId]);
});

test('ruang belajar yang belum dipakai sesi bisa dihapus', function () {
    $this->delete(route('admin.manajemenJadwal.ruang.destroy', $this->roomId))
        ->assertRedirect(route('admin.manajemenJadwal', ['tab' => 'ruang']));

    $this->assertDatabaseMissing('rooms', ['id' => $this->roomId]);
});

test('halaman ruang menampilkan daftar ruang belajar', function () {
    $this->get(route('admin.manajemenJadwal', ['tab' => 'ruang']))
        ->assertOk()
        ->assertSee('Daftar Ruang Belajar')
        ->assertSee('R. Bima')
        ->assertSee('Lt. 2')
        ->assertSee('Tambah Ruang Belajar');
});

test('generate tanpa aturan jadwal menjelaskan bahwa aturan harus dibuat dulu', function () {
    DB::table('schedule_rules')->delete();

    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ])->assertRedirect(route('admin.manajemenJadwal', [
        'tab' => 'sesi',
        'dari' => '2026-10-01',
        'sampai' => '2026-10-31',
    ]));

    $this->assertDatabaseCount('class_sessions', 0);

    $this->followingRedirects()
        ->post(route('admin.manajemenJadwal.sesi.generate'), [
            'from_date' => '2026-10-01',
            'to_date' => '2026-10-31',
        ])
        ->assertOk()
        ->assertSee('Belum ada aturan jadwal mingguan')
        ->assertSee('Buat Aturan Jadwal');
});

test('ringkasan generate menampilkan rincian sesi yang dilewati', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ]);

    $this->followingRedirects()
        ->post(route('admin.manajemenJadwal.sesi.generate'), [
            'from_date' => '2026-10-01',
            'to_date' => '2026-10-31',
        ])
        ->assertOk()
        ->assertSee('0 sesi terjadwal dibuat dari aturan jadwal.')
        ->assertSee('Lihat rincian')
        ->assertSee('sesi sudah pernah dibuat');
});

test('generate mempertahankan rentang tanggal agar hasil generate langsung terlihat', function () {
    $this->post(route('admin.manajemenJadwal.sesi.generate'), [
        'from_date' => '2026-10-01',
        'to_date' => '2026-10-31',
    ]);

    $this->followingRedirects()
        ->post(route('admin.manajemenJadwal.sesi.generate'), [
            'from_date' => '2026-10-01',
            'to_date' => '2026-10-31',
        ])
        ->assertOk()
        ->assertSee('Kamis, 01 Okt 2026')
        ->assertSee('Kamis, 29 Okt 2026');
});

test('tab sesi terjadwal menunjuk ke tab aturan saat belum ada aturan', function () {
    DB::table('schedule_rules')->delete();

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi']))
        ->assertOk()
        ->assertSee('Belum ada aturan jadwal mingguan')
        ->assertSee('Buat aturan dulu di tab');
});

test('tab sesi terjadwal menampilkan daftar sesi pada rentang bulan berjalan', function () {
    DB::table('class_sessions')->insert([
        'class_group_id' => $this->classId,
        'subject_id' => $this->subjectId,
        'staff_id' => $this->staffId,
        'room_id' => $this->roomId,
        'study_shift_id' => $this->shiftId,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('admin.manajemenJadwal', ['tab' => 'sesi']))
        ->assertOk()
        ->assertSee('Matematika')
        ->assertSee('1 Sesi');
});
