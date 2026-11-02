<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));

    seedKlinikPrData();
});

test('qualifix returns a successful response', function () {
    $response = $this->get('/admin/qualifix');

    $response->assertStatus(200);
    $response->assertSee('QualiFix');
    $response->assertSee('Daftar pertanyaan dan sesi konsultasi tugas sekolah siswa.');
    $response->assertSee('Total Masuk');
    $response->assertSee('Menunggu Tentor');
    $response->assertSee('Perlu Respon');
    $response->assertSee('Terselesaikan');
    $response->assertSee('Ketersediaan Tentor');
    $response->assertSee('Tentor Pendamping Klinik PR');
});

test('qualifix is reachable from its named route', function () {
    $this->get(route('admin.qualifix'))->assertStatus(200);
});

test('qualifix menu is active on the page', function () {
    $this->get(route('admin.qualifix'))
        ->assertStatus(200)
        ->assertSee('QualiFix');
});

test('qualifix lists consultation tickets from the database', function () {
    $response = $this->get(route('admin.qualifix'));

    $response->assertStatus(200);
    $response->assertSee('#KPR-0824');
    $response->assertSee('Integral Substitusi Trigonometri');
    $response->assertSee('Arya Danendra');
    $response->assertSee('Tugaskan Tentor');
    $response->assertSee('Tata Nama Senywa Benzena');
    $response->assertSee('Tentor: Kak Gilang');
    $response->assertSee('Lihat Detail');
    $response->assertSee('Hortatory Exposition Text');
    $response->assertSee('Lihat Solusi');
    $response->assertSee('Tiket Konsultasi');
});

test('qualifix shows standby tutors and ticket counters', function () {
    $response = $this->get(route('admin.qualifix'));

    $response->assertStatus(200);
    $response->assertSee('2 Standby');
    $response->assertSee('Kak Gilang');
    $response->assertSee('Kak Sarah');
    $response->assertSee('Kak Dimas');
    $response->assertSee('Membimbing');
    $response->assertSee('Tersedia');
    $response->assertSee('Semua');
    $response->assertSee('Selesai');
});

/**
 * Seed data klinik PR (QualiFix) ke database untuk uji halaman admin.
 */
function seedKlinikPrData(): void
{
    $now = now();

    DB::table('academic_periods')->insert([
        'academic_year' => '2025/2026',
        'semester' => 'ganjil',
        'starts_on' => '2025-07-01',
        'ends_on' => '2025-12-31',
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $periodId = DB::table('academic_periods')->where('academic_year', '2025/2026')
        ->where('semester', 'ganjil')->value('id');

    $subjects = [
        'MTK' => 'Matematika',
        'FIS' => 'Fisika',
        'KIM' => 'Kimia',
        'BING' => 'B. Inggris',
        'BIND' => 'B. Indonesia',
        'BIO' => 'Biologi',
    ];
    $subjectIds = [];
    foreach ($subjects as $code => $name) {
        $subjectIds[$code] = DB::table('subjects')->insertGetId([
            'code' => $code,
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $classIds = [];
    foreach (['12-MIA-1' => 'SMA', '11-MIA-1' => 'SMA', '10-MIA-1' => 'SMA', '9-SMP-1' => 'SMP'] as $code => $level) {
        $classIds[$code] = DB::table('class_groups')->insertGetId([
            'academic_period_id' => $periodId,
            'code' => $code,
            'education_level' => $level,
            'grade' => Str::substr($code, 0, 2),
            'major' => 'IPA',
            'capacity' => 30,
            'status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $studentNames = [
        'Arya Danendra', 'Zahra Annisa', 'Bima Aryasatya', 'Rizky Maulana', 'Salsabila Ayu',
        'Nadira Safitri', 'Danu Prakoso', 'Ananda Putri', 'Rafi Ardiansyah', 'Kirana Maharani',
    ];
    $studentIds = [];
    $nis = 1;
    foreach ($studentNames as $name) {
        $studentIds[$name] = DB::table('students')->insertGetId([
            'full_name' => $name,
            'nis' => 'NIS'.str_pad((string) $nis++, 4, '0', STR_PAD_LEFT),
            'status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $tickets = [
        ['KPR-0824', 'Integral Substitusi Trigonometri', 'MTK', '12-MIA-1', 'Arya Danendra', 'menunggu_tentor', null],
        ['KPR-0823', 'Hukum Induksi Faraday', 'FIS', '11-MIA-1', 'Zahra Annisa', 'menunggu_tentor', null],
        ['KPR-0822', 'Limit Fungsi Trigonometri', 'MTK', '12-MIA-1', 'Bima Aryasatya', 'menunggu_tentor', null],
        ['KPR-0821', 'Tata Nama Senywa Benzena', 'KIM', '12-MIA-1', 'Rizky Maulana', 'sedang_dibahas', 'Kak Gilang'],
        ['KPR-0820', 'Persamaan Garis Lurus', 'MTK', '10-MIA-1', 'Salsabila Ayu', 'menunggu_tentor', null],
        ['KPR-0819', 'Hortatory Exposition Text', 'BING', '10-MIA-1', 'Nadira Safitri', 'selesai', null],
        ['KPR-0818', 'Penyusunan Teks Eksposisi', 'BIND', '9-SMP-1', 'Danu Prakoso', 'selesai', null],
        ['KPR-0817', 'Stoikiometri & Persamaan Reaksi', 'KIM', '11-MIA-1', 'Ananda Putri', 'selesai', null],
        ['KPR-0816', 'Gerak Parabola Lintasan Proyektil', 'FIS', '11-MIA-1', 'Rafi Ardiansyah', 'selesai', null],
        ['KPR-0815', 'Sistem Peredaran Darah Manusia', 'BIO', '10-MIA-1', 'Kirana Maharani', 'selesai', null],
    ];
    $ticketIds = [];
    foreach ($tickets as [$number, $title, $subject, $class, $student, $status, $tentor]) {
        $ticketIds[$number] = DB::table('qualifix_tickets')->insertGetId([
            'ticket_number' => $number,
            'student_id' => $studentIds[$student],
            'subject_id' => $subjectIds[$subject],
            'class_group_id' => $classIds[$class],
            'title' => $title,
            'question' => 'Contoh pertanyaan tentang '.$title,
            'status' => $status,
            'submitted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    $tentors = [
        'Kak Gilang' => ['Matematika', 'membimbing', 'KPR-0821'],
        'Kak Sarah' => ['B. Inggris & B. Indo', 'tersedia', null],
        'Kak Dimas' => ['Kimia & Biologi', 'tersedia', null],
    ];
    foreach ($tentors as $nama => [$bidang, $kondisi, $assignedTicket]) {
        $userId = DB::table('users')->insertGetId([
            'name' => $nama,
            'email' => Str::lower(str_replace(' ', '.', $nama)).'@qualify.test',
            'phone' => '08'.fake()->numerify('##########'),
            'password' => bcrypt('rahasia'),
            'role' => 'tentor',
            'status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $staffId = DB::table('staff_profiles')->insertGetId([
            'user_id' => $userId,
            'full_name_with_title' => $nama,
            'job_title' => $bidang,
            'status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($assignedTicket) {
            DB::table('qualifix_assignments')->insert([
                'ticket_id' => $ticketIds[$assignedTicket],
                'staff_id' => $staffId,
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
