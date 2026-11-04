<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Room;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Ir. Hendra Gunawan',
    ]);

    $this->actingAs($this->user);

    $this->guardian = Guardian::create([
        'user_id' => $this->user->id,
        'name' => 'Ir. Hendra Gunawan',
        'occupation' => 'Wiraswasta',
        'phone_wa' => '+62 812-4455-8891',
        'email' => 'hendra.gunawan@email.com',
        'address' => 'Jl. Gandaria Tengah III No. 14, Jakarta Selatan',
    ]);

    $this->anak = Student::create([
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
        'school_name' => 'SMAN 8 Jakarta',
        'school_grade' => '12',
        'education_level' => 'SMA',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $this->anak->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
    ]);
});

test('kehadiran anak page returns a successful response', function () {
    $response = $this->get('/wali/kehadiran');

    $response->assertStatus(200);
    $response->assertSee('Monitoring Kehadiran');
    $response->assertSee('Kehadiran Fisik');
    $response->assertSee('Riwayat Kehadiran Ananda');
    $response->assertSee('Kehadiran Terakhir');
    $response->assertSee('Hubungi Admin Bimbel');
    $response->assertSee('Notifikasi WhatsApp Otomatis');
});

test('kehadiran anak is reachable from its named route', function () {
    $this->get(route('wali.kehadiran'))->assertStatus(200);
});

test('overlay pengajuan izin tersedia dari tombol ajukan izin', function () {
    $this->get('/wali/kehadiran')
        ->assertStatus(200)
        ->assertSee('Ajukan Izin Sekarang')
        ->assertSee('data-ortu-izin-open', false)
        ->assertSee('Pengajuan Izin / Sakit')
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('Pilih Jenis Ketidakhadiran')
        ->assertSee('Keterangan Singkat')
        ->assertSee('Kirim konfirmasi otomatis via WhatsApp ke nomor terdaftar')
        ->assertSee('Kirim Izin')
        ->assertSee('data-ortu-izin-close', false);
});

test('overlay pengajuan izin menyediakan pilihan jenis izin dan sakit', function () {
    $this->get('/wali/kehadiran')
        ->assertStatus(200)
        ->assertSee('Lomba / Acara')
        ->assertSee('Pemulihan Medis');
});

test('dropdown pilih sesi menampilkan sesi kelas mendatang anak', function () {
    $tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $staff = StaffProfile::create([
        'user_id' => $tentor->id,
        'full_name_with_title' => 'Farhan Kamil, S.Si.',
        'job_title' => 'Master Tentor Fisika',
        'status' => 'aktif',
    ]);

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

    Enrollment::create([
        'student_id' => $this->anak->id,
        'class_group_id' => $kelas->id,
        'started_on' => '2026-07-14',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'FIS', 'name' => 'Fisika Saintek']);
    $ruang = Room::create(['name' => 'Ruangan Eureka', 'floor' => 'Lt. 2', 'status' => 'aktif']);

    ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'room_id' => $ruang->id,
        'session_date' => today()->addDay()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'topic' => 'Gelembung & Hukum Archimedes',
        'status' => 'terjadwal',
    ]);

    $this->get('/wali/kehadiran')
        ->assertStatus(200)
        ->assertSee('Fisika Saintek (', false);
});
