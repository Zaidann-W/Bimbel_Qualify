<?php

use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $this->actingAs($this->user);

    $this->staff = StaffProfile::create([
        'user_id' => $this->user->id,
        'employee_number' => 'T001',
        'full_name_with_title' => 'Kak Hendra',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    $siswa = Student::create([
        'full_name' => 'Aisyah Putri Azzahra',
        'nisn' => '0098123471',
        'status' => 'aktif',
    ]);

    $mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    $this->ticketMenunggu = QualifixTicket::create([
        'ticket_number' => 'KPR-0101',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => null,
        'title' => 'Barisan & Deret Geometri Tak Hingga',
        'question' => 'Kak Hendra, mohon bantuannya untuk soal nomor 6 deret geometri tak hingga.',
        'status' => 'menunggu_tentor',
    ]);
    $this->ticketMenunggu->staff()->attach($this->staff->id);

    QualifixTicket::create([
        'ticket_number' => 'KPR-0102',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => null,
        'title' => 'Integral Parsial',
        'question' => 'Bagaimana memilih u dan dv untuk integral parsial?',
        'status' => 'selesai',
        'resolved_at' => now(),
    ])->staff()->attach($this->staff->id);
});

test('qualifix tentor halaman merespons sukses dan menampilkan data dari db', function () {
    $this->get('/tentor/qualifix')
        ->assertStatus(200)
        ->assertSee('Pertanyaan Menunggu')
        ->assertSee('Terkoreksi Pekan Ini')
        ->assertSee('Antrean Pertanyaan')
        ->assertSee('Barisan & Deret Geometri Tak Hingga')
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('Menunggu')
        ->assertSee('Matematika Saintek');
});

test('qualifix tentor tercapai melalui nama route', function () {
    $this->get(route('tentor.qualifix'))->assertStatus(200);
});

test('qualifix tentor menampilkan empty state tanpa antrean', function () {
    QualifixTicket::query()->delete();

    $this->get('/tentor/qualifix')
        ->assertStatus(200)
        ->assertSee('Belum Ada Antrean');
});
