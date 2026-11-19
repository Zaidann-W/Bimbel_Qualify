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

    $this->ticketDijawab = QualifixTicket::create([
        'ticket_number' => 'KPR-0102',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => null,
        'title' => 'Matriks dan Determinan',
        'question' => 'Bagaimana menentukan determinan matriks 3x3?',
        'status' => 'sedang_dibahas',
    ]);
    $this->ticketDijawab->staff()->attach($this->staff->id);

    $this->jawabanHendra = QualifixAnswer::create([
        'ticket_id' => $this->ticketDijawab->id,
        'staff_id' => $this->staff->id,
        'body' => 'Gunakan metode Sarrus dengan menduplikasi dua kolom pertama.',
        'status' => 'terkirim',
        'sent_at' => now(),
    ]);

    $this->ticketSelesai = QualifixTicket::create([
        'ticket_number' => 'KPR-0103',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'class_group_id' => null,
        'title' => 'Integral Parsial',
        'question' => 'Bagaimana memilih u dan dv untuk integral parsial?',
        'status' => 'selesai',
        'resolved_at' => now(),
    ]);
    $this->ticketSelesai->staff()->attach($this->staff->id);

    QualifixAnswer::create([
        'ticket_id' => $this->ticketSelesai->id,
        'staff_id' => $this->staff->id,
        'body' => 'Gunakan aturan LIATE (Logaritma, Invers trigonometri, Aljabar, Trigonometri, Eksponensial).',
        'status' => 'terkirim',
        'sent_at' => now(),
    ]);
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

test('default pilihan tab berada pada semua', function () {
    $response = $this->get(route('tentor.qualifix'));

    $response->assertStatus(200);
    $response->assertViewHas('activeTab', 'semua');
    // Tab 'Semua' memiliki class active
    $response->assertSee('Semua');
});

test('dapat memfilter pertanyaan berdasarkan tab menunggu respon sudah dijawab dan selesai', function () {
    // Tab Menunggu
    $resMenunggu = $this->get(route('tentor.qualifix', ['tab' => 'menunggu']));
    $resMenunggu->assertStatus(200);
    $resMenunggu->assertViewHas('activeTab', 'menunggu');
    $resMenunggu->assertSee('Barisan & Deret Geometri Tak Hingga');
    $resMenunggu->assertDontSee('Matriks dan Determinan');
    $resMenunggu->assertDontSee('Integral Parsial');

    // Tab Sudah Dijawab
    $resDijawab = $this->get(route('tentor.qualifix', ['tab' => 'sudah_dijawab']));
    $resDijawab->assertStatus(200);
    $resDijawab->assertViewHas('activeTab', 'sudah_dijawab');
    $resDijawab->assertSee('Matriks dan Determinan');
    $resDijawab->assertDontSee('Barisan & Deret Geometri Tak Hingga');
    $resDijawab->assertDontSee('Integral Parsial');

    // Tab Selesai
    $resSelesai = $this->get(route('tentor.qualifix', ['tab' => 'selesai']));
    $resSelesai->assertStatus(200);
    $resSelesai->assertViewHas('activeTab', 'selesai');
    $resSelesai->assertSee('Integral Parsial');
    $resSelesai->assertDontSee('Barisan & Deret Geometri Tak Hingga');
    $resSelesai->assertDontSee('Matriks dan Determinan');
});

test('menampilkan semua pertanyaan bukan hanya mapel tentor', function () {
    $mapelFisika = Subject::create(['code' => 'FIS', 'name' => 'Fisika Dasar']);
    $siswa = Student::first();

    // Buat tiket mapel Fisika tanpa tentor
    $tiketFisika = QualifixTicket::create([
        'ticket_number' => 'KPR-FIS-001',
        'student_id' => $siswa->id,
        'subject_id' => $mapelFisika->id,
        'title' => 'Hukum Newton II Tentang Gerak',
        'question' => 'Bagaimana jika ada gaya gesek kinetis?',
        'status' => 'menunggu_tentor',
    ]);

    $response = $this->get(route('tentor.qualifix'));
    $response->assertStatus(200);
    // Pertanyaan Fisika harus tetap muncul di daftar antrean tentor matematika
    $response->assertSee('Hukum Newton II Tentang Gerak');
    $response->assertSee('Fisika Dasar');
});

test('dapat memencet pertanyaan spesifik dan menampilkan detail serta pemberi jawaban jika sudah dijawab', function () {
    $response = $this->get(route('tentor.qualifix', ['ticket_id' => $this->ticketDijawab->id]));
    $response->assertStatus(200);
    $response->assertSee('Matriks dan Determinan');
    $response->assertSee('Bagaimana menentukan determinan matriks 3x3?');
    $response->assertSee('Kak Hendra');
    $response->assertSee('Gunakan metode Sarrus dengan menduplikasi dua kolom pertama.');
});

test('tentor penjawab dapat mengedit jawabannya pada tiket yang berstatus sudah dijawab', function () {
    $response = $this->put(route('tentor.qualifix.answer.update', $this->jawabanHendra->id), [
        'body' => 'Revisi: Gunakan metode Sarrus untuk 3x3 atau ekspansi kofaktor.',
    ]);

    $response->assertRedirect(route('tentor.qualifix', ['ticket_id' => $this->ticketDijawab->id]));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('qualifix_answers', [
        'id' => $this->jawabanHendra->id,
        'body' => 'Revisi: Gunakan metode Sarrus untuk 3x3 atau ekspansi kofaktor.',
    ]);
});

test('tentor lain tidak dapat mengedit jawaban tentor lain', function () {
    $tentorLainUser = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    StaffProfile::create([
        'user_id' => $tentorLainUser->id,
        'employee_number' => 'T999',
        'full_name_with_title' => 'Kak Rina',
        'status' => 'aktif',
    ]);

    $this->actingAs($tentorLainUser);

    $this->put(route('tentor.qualifix.answer.update', $this->jawabanHendra->id), [
        'body' => 'Percobaan edit tidak sah.',
    ])->assertStatus(403);
});

test('tentor lain dapat menambahkan jawaban pada tiket berstatus sudah dijawab', function () {
    $tentorLainUser = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $tentorLainStaff = StaffProfile::create([
        'user_id' => $tentorLainUser->id,
        'employee_number' => 'T999',
        'full_name_with_title' => 'Kak Rina',
        'status' => 'aktif',
    ]);

    $this->actingAs($tentorLainUser);

    $response = $this->post(route('tentor.qualifix.answer', $this->ticketDijawab->id), [
        'body' => 'Tambahan dari Kak Rina: Anda juga bisa menggunakan metode operasi baris elementer.',
    ]);

    $response->assertRedirect(route('tentor.qualifix', ['ticket_id' => $this->ticketDijawab->id]));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('qualifix_answers', [
        'ticket_id' => $this->ticketDijawab->id,
        'staff_id' => $tentorLainStaff->id,
        'body' => 'Tambahan dari Kak Rina: Anda juga bisa menggunakan metode operasi baris elementer.',
    ]);
});

test('pada tiket selesai jawaban tidak dapat diganti maupun dijawab oleh tentor lain', function () {
    $jawabanSelesai = QualifixAnswer::where('ticket_id', $this->ticketSelesai->id)->first();

    // 1. Tentor penjawab tidak bisa mengedit jawaban saat tiket selesai
    $this->put(route('tentor.qualifix.answer.update', $jawabanSelesai->id), [
        'body' => 'Coba edit saat tiket selesai.',
    ])->assertStatus(403);

    // 2. Tentor lain tidak bisa menambah jawaban saat tiket selesai
    $tentorLainUser = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    StaffProfile::create([
        'user_id' => $tentorLainUser->id,
        'employee_number' => 'T888',
        'full_name_with_title' => 'Kak Budi',
        'status' => 'aktif',
    ]);
    $this->actingAs($tentorLainUser);

    $this->post(route('tentor.qualifix.answer', $this->ticketSelesai->id), [
        'body' => 'Coba tambah jawaban saat tiket selesai.',
    ])->assertStatus(403);
});

test('tentor dapat mengirim jawaban ke siswa dengan lampiran berkas', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->image('coretan_solusi.jpg', 600, 600)->size(500);

    $response = $this->post(route('tentor.qualifix.answer', $this->ticketMenunggu->id), [
        'body' => 'Rumus jumlah deret geometri tak hingga adalah S = a / (1 - r).',
        'files' => [$file],
    ]);

    $response->assertRedirect(route('tentor.qualifix', ['ticket_id' => $this->ticketMenunggu->id]));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('qualifix_answers', [
        'ticket_id' => $this->ticketMenunggu->id,
        'staff_id' => $this->staff->id,
        'body' => 'Rumus jumlah deret geometri tak hingga adalah S = a / (1 - r).',
    ]);

    $this->assertDatabaseHas('qualifix_tickets', [
        'id' => $this->ticketMenunggu->id,
        'status' => 'sedang_dibahas',
    ]);
});

test('tentor dapat mengklaim tiket pertanyaan', function () {
    $mapel = Subject::first();
    $siswa = Student::first();
    $tiketBebas = QualifixTicket::create([
        'ticket_number' => 'KPR-KLAIM-001',
        'student_id' => $siswa->id,
        'subject_id' => $mapel->id,
        'title' => 'Pertanyaan Terbuka',
        'question' => 'Mohon bantuan pembahasan.',
        'status' => 'menunggu_tentor',
    ]);

    $response = $this->post(route('tentor.qualifix.claim', $tiketBebas->id));
    $response->assertRedirect(route('tentor.qualifix', ['ticket_id' => $tiketBebas->id]));

    $this->assertDatabaseHas('qualifix_assignments', [
        'ticket_id' => $tiketBebas->id,
        'staff_id' => $this->staff->id,
    ]);
});
