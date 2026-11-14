<?php

use App\Models\QualifixAnswer;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');

    $this->siswaUser = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $this->siswa = Student::create([
        'user_id' => $this->siswaUser->id,
        'full_name' => 'Aisyah Putri Azzahra',
        'nisn' => '0098123471',
        'status' => 'aktif',
    ]);

    $this->mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);

    $this->tentorUser = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $this->tentor = StaffProfile::create([
        'user_id' => $this->tentorUser->id,
        'employee_number' => 'T001',
        'full_name_with_title' => 'Kak Hendra',
        'job_title' => 'Master Tentor Matematika',
        'status' => 'aktif',
    ]);

    // Tentor lain (bukan penugat) untuk uji otorisasi.
    $this->tentorLainUser = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $this->tentorLain = StaffProfile::create([
        'user_id' => $this->tentorLainUser->id,
        'employee_number' => 'T002',
        'full_name_with_title' => 'Kak Rina',
        'job_title' => 'Master Tentor Fisika',
        'status' => 'aktif',
    ]);
});

test('siswa dapat mengajukan pertanyaan klinik pr dan tiket ditugaskan ke tentor', function () {
    $this->actingAs($this->siswaUser);

    $response = $this->post(route('siswa.qualifix.store'), [
        'subject_id' => $this->mapel->id,
        'title' => 'Barisan & Deret Geometri Tak Hingga',
        'question' => 'Kak Hendra, mohon bantuannya untuk soal nomor 6 deret geometri tak hingga.',
        'files' => [UploadedFile::fake()->image('pertanyaan.jpg')->size(100)],
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('sukses');

    $this->assertDatabaseHas('qualifix_tickets', [
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Barisan & Deret Geometri Tak Hingga',
        'status' => 'menunggu_tentor',
    ]);

    $ticket = QualifixTicket::where('student_id', $this->siswa->id)->latest()->first();
    $this->assertNotNull($ticket->ticket_number);
    $this->assertStringStartsWith('KPR-', $ticket->ticket_number);
    $this->assertNotNull($ticket->submitted_at);

    // Tiket harus ditugaskan ke tentor aktif (fallback beban paling ringan).
    $this->assertDatabaseHas('qualifix_assignments', [
        'ticket_id' => $ticket->id,
        'staff_id' => $this->tentor->id,
    ]);

    // Lampiran tercatat.
    $this->assertDatabaseHas('qualifix_attachments', [
        'ticket_id' => $ticket->id,
        'answer_id' => null,
    ]);
});

test('siswa tanpa data siswa tidak boleh mengajukan', function () {
    $orangLain = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    // User ini tidak punya relasi Student.
    $this->actingAs($orangLain);

    $this->post(route('siswa.qualifix.store'), [
        'subject_id' => $this->mapel->id,
        'title' => 'Judul',
        'question' => 'Pertanyaan',
    ])->assertStatus(403);
});

test('tentor dapat menjawab pertanyaan yang ditugaskan kepadanya', function () {
    $ticket = QualifixTicket::create([
        'ticket_number' => 'KPR-TEST-001',
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Uji Jawaban',
        'question' => 'Pertanyaan uji',
        'status' => 'menunggu_tentor',
        'submitted_at' => now(),
    ]);
    $ticket->staff()->attach($this->tentor->id);

    $this->actingAs($this->tentorUser);

    $response = $this->post(route('tentor.qualifix.answer', $ticket->id), [
        'body' => 'Ini adalah jawaban lengkap dari tentor.',
        'files' => [UploadedFile::fake()->image('jawaban.jpg')->size(100)],
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('sukses');

    $this->assertDatabaseHas('qualifix_answers', [
        'ticket_id' => $ticket->id,
        'staff_id' => $this->tentor->id,
        'body' => 'Ini adalah jawaban lengkap dari tentor.',
        'status' => 'terkirim',
    ]);

    // Status tiket berubah menjadi sedang_dibahas setelah dijawab.
    $this->assertDatabaseHas('qualifix_tickets', [
        'id' => $ticket->id,
        'status' => 'sedang_dibahas',
    ]);

    $this->assertDatabaseHas('qualifix_attachments', [
        'ticket_id' => $ticket->id,
        'answer_id' => QualifixAnswer::where('ticket_id', $ticket->id)->value('id'),
    ]);
});

test('tentor tidak boleh menjawab tiket yang bukan ditugaskan kepadanya', function () {
    $ticket = QualifixTicket::create([
        'ticket_number' => 'KPR-TEST-002',
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Uji Otorisasi',
        'question' => 'Pertanyaan',
        'status' => 'menunggu_tentor',
    ]);
    $ticket->staff()->attach($this->tentorLain->id);

    $this->actingAs($this->tentorUser);

    $this->post(route('tentor.qualifix.answer', $ticket->id), [
        'body' => 'Jawaban tidak sah.',
    ])->assertStatus(403);
});

test('tentor dapat menandai tiket selesai', function () {
    $ticket = QualifixTicket::create([
        'ticket_number' => 'KPR-TEST-003',
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Uji Selesai',
        'question' => 'Pertanyaan',
        'status' => 'sedang_dibahas',
        'submitted_at' => now(),
    ]);
    $ticket->staff()->attach($this->tentor->id);

    $this->actingAs($this->tentorUser);

    $this->patch(route('tentor.qualifix.resolve', $ticket->id))
        ->assertStatus(302)
        ->assertSessionHas('sukses');

    $this->assertDatabaseHas('qualifix_tickets', [
        'id' => $ticket->id,
        'status' => 'selesai',
    ]);
    $this->assertNotNull($ticket->fresh()->resolved_at);
});

test('admin dapat menugaskan tentor ke tiket', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->actingAs($admin);

    $ticket = QualifixTicket::create([
        'ticket_number' => 'KPR-TEST-004',
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Uji Tugaskan',
        'question' => 'Pertanyaan',
        'status' => 'menunggu_tentor',
    ]);

    $this->post(route('admin.qualifix.assign', $ticket->id), [
        'staff_id' => $this->tentor->id,
    ])->assertStatus(302)->assertSessionHas('sukses');

    $this->assertDatabaseHas('qualifix_assignments', [
        'ticket_id' => $ticket->id,
        'staff_id' => $this->tentor->id,
    ]);
});

test('admin dapat mengubah status tiket', function () {
    $admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->actingAs($admin);

    $ticket = QualifixTicket::create([
        'ticket_number' => 'KPR-TEST-005',
        'student_id' => $this->siswa->id,
        'subject_id' => $this->mapel->id,
        'title' => 'Uji Status',
        'question' => 'Pertanyaan',
        'status' => 'menunggu_tentor',
    ]);

    $this->patch(route('admin.qualifix.status', $ticket->id), [
        'status' => 'selesai',
    ])->assertStatus(302)->assertSessionHas('sukses');

    $this->assertDatabaseHas('qualifix_tickets', [
        'id' => $ticket->id,
        'status' => 'selesai',
    ]);
    $this->assertNotNull($ticket->fresh()->resolved_at);
});

test('nomor tiket unik dan berurutan per bulan', function () {
    $this->actingAs($this->siswaUser);

    $this->post(route('siswa.qualifix.store'), [
        'subject_id' => $this->mapel->id,
        'title' => 'Tiket Pertama',
        'question' => 'Pertanyaan pertama',
    ]);

    $this->post(route('siswa.qualifix.store'), [
        'subject_id' => $this->mapel->id,
        'title' => 'Tiket Kedua',
        'question' => 'Pertanyaan kedua',
    ]);

    $tiket1 = QualifixTicket::where('title', 'Tiket Pertama')->value('ticket_number');
    $tiket2 = QualifixTicket::where('title', 'Tiket Kedua')->value('ticket_number');

    $this->assertNotNull($tiket1);
    $this->assertNotNull($tiket2);
    $this->assertNotSame($tiket1, $tiket2);
});
