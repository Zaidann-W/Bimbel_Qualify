<?php

use App\Models\AcademicPeriod;
use App\Models\Announcement;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\File;
use App\Models\Material;
use App\Models\QualifixTicket;
use App\Models\Room;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'siswa', 'status' => 'aktif']));
});

test('dashboard siswa menampilkan judul dan empty state tanpa data kelas', function () {
    $this->get('/siswa')
        ->assertStatus(200)
        ->assertSee('Jadwal Belajar Pekan Ini')
        ->assertSee('Materi &amp; Modul Baru Rilis', false)
        ->assertSee('Pengumuman Pergantian Jam')
        ->assertSee('Belum ada jadwal kelas untuk pekan ini.')
        ->assertSee('Belum ada modul yang dirilis untuk kelasmu.');
});

test('dashboard siswa menampilkan data dari basis data', function () {
    $tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $staff = StaffProfile::create([
        'user_id' => $tentor->id,
        'full_name_with_title' => 'Farhan Kamil, S.Si.',
        'job_title' => 'Master Tentor Matematika',
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

    $siswa = Student::create([
        'user_id' => auth()->id(),
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
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

    $sesi = ClassSession::create([
        'class_group_id' => $kelas->id,
        'subject_id' => $mapel->id,
        'staff_id' => $staff->id,
        'room_id' => $ruang->id,
        'session_date' => today()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:00:00',
        'topic' => 'Integral Substitusi & Parsial',
        'status' => 'selesai',
    ]);

    StudentAttendance::create([
        'class_session_id' => $sesi->id,
        'student_id' => $siswa->id,
        'status' => 'hadir',
    ]);

    $berkas = File::create([
        'original_name' => 'modul-08.pdf',
        'path' => 'materials/modul-08.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 4404019,
    ]);

    $material = Material::create([
        'title' => 'Modul 08: Trik Cepat Integral',
        'description' => 'Rumus cepat limit wajib.',
        'subject_id' => $mapel->id,
        'file_id' => $berkas->id,
        'status' => 'terbit',
        'published_at' => now(),
    ]);

    $material->classGroups()->attach($kelas->id);

    QualifixTicket::create([
        'ticket_number' => 'KPR-0001',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => $kelas->id,
        'title' => 'Cara cepat limit 0/0',
        'question' => 'Kapan pakai L\'Hopital?',
        'status' => 'menunggu_tentor',
    ]);

    Announcement::create([
        'title' => 'Pergantian Jam Fisika Saintek',
        'body' => 'Jadwal digeser ke pukul 15.30 WIB.',
        'priority' => 'penting',
        'class_group_id' => $kelas->id,
        'class_session_id' => $sesi->id,
        'room_id' => $ruang->id,
        'published_at' => now(),
    ]);

    $this->get('/siswa')
        ->assertStatus(200)
        ->assertSee('Halo, Aisyah!')
        ->assertSee('Kelas 12 SMA – IPA')
        ->assertSee('Matematika Saintek')
        ->assertSee('Integral Substitusi &amp; Parsial', false)
        ->assertSee('Ruang Eureka Lt. 2')
        ->assertSee('1 Hadir / 1 Sesi')
        ->assertSee('100.0%')
        ->assertSee('Modul 08: Trik Cepat Integral')
        ->assertSee('PDF • 4.2 MB')
        ->assertSee('Pergantian Jam Fisika Saintek')
        ->assertSee('1 Menunggu');
});

test('dashboard siswa is reachable from its named route', function () {
    $this->get(route('siswa.dashboard'))->assertStatus(200);
});
