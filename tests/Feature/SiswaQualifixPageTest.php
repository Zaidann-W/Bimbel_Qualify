<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\Enrollment;
use App\Models\QualifixTicket;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $this->actingAs($this->user);

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
        'user_id' => $this->user->id,
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

    QualifixTicket::create([
        'ticket_number' => 'KPR-0001',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => $kelas->id,
        'title' => 'Cara cepat limit 0/0',
        'question' => 'Kapan pakai L\'Hopital?',
        'status' => 'menunggu_tentor',
    ]);

    QualifixTicket::create([
        'ticket_number' => 'KPR-0002',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => $kelas->id,
        'title' => 'Integral parsial',
        'question' => 'Bagaimana pilih u dan dv?',
        'status' => 'selesai',
    ]);
});

test('qualifix siswa page returns a successful response', function () {
    $this->get('/siswa/qualifix')
        ->assertStatus(200)
        ->assertSee('Total Diajukan')
        ->assertSee('Menunggu Jawaban')
        ->assertSee('Cara cepat limit 0/0')
        ->assertSee('Panduan Tanya Soal')
        ->assertSee('Ajukan Pertanyaan PR Baru');
});

test('qualifix siswa is reachable from its named route', function () {
    $this->get(route('siswa.qualifix'))->assertStatus(200);
});

test('qualifix menampilkan empty state tanpa pertanyaan', function () {
    QualifixTicket::query()->delete();
    $this->get('/siswa/qualifix')
        ->assertStatus(200)
        ->assertSee('Belum ada pertanyaan yang diajukan.');
});
