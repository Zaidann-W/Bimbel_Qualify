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

    $groupId = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $periodId,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subjectId = DB::table('subjects')->insertGetId([
        'code' => 'MTK',
        'name' => 'Matematika Wajib',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $roomId = DB::table('rooms')->insertGetId([
        'name' => 'Ruang Bima',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tutorUserId = User::factory()->create(['role' => 'tentor', 'status' => 'aktif'])->id;

    $staffId = DB::table('staff_profiles')->insertGetId([
        'user_id' => $tutorUserId,
        'full_name_with_title' => 'Dimas Wahyu, S.Pd',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aisyah Nurul Hikmah',
        'school_name' => 'SMAN 1 Yogyakarta',
        'school_grade' => '12',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $groupId,
        'started_on' => now()->subMonth()->toDateString(),
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sessionId = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $groupId,
        'subject_id' => $subjectId,
        'staff_id' => $staffId,
        'room_id' => $roomId,
        'session_date' => now()->toDateString(),
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'topic' => 'Kalkulus Sederhana',
        'status' => 'berlangsung',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('student_attendance')->insert([
        'class_session_id' => $sessionId,
        'student_id' => $studentId,
        'status' => 'hadir',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $invoiceId = DB::table('invoices')->insertGetId([
        'invoice_number' => 'INV-202610-0001',
        'student_id' => $studentId,
        'academic_period_id' => $periodId,
        'type' => 'spp_bulanan',
        'amount' => 500000,
        'status' => 'lunas',
        'paid_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('payments')->insert([
        'invoice_id' => $invoiceId,
        'payment_method' => 'cash',
        'amount' => 500000,
        'status' => 'berhasil',
        'paid_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('qualifix_tickets')->insert([
        'ticket_number' => 'QF-202610-0001',
        'student_id' => $studentId,
        'subject_id' => $subjectId,
        'class_group_id' => $groupId,
        'title' => 'Limit bentuk 0/0',
        'question' => 'Bagaimana langkahnya?',
        'status' => 'menunggu_tentor',
        'submitted_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('applications')->insert([
        'registration_number' => 'REG-202610-0001',
        'full_name' => 'Rayhan Pratama',
        'school_name' => 'SMP IT Al-Fityan',
        'school_grade' => '9',
        'status' => 'menunggu_review',
        'submitted_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('admin dashboard menampilkan angka dari database', function () {
    $response = $this->get('/admin');

    $response->assertOk();
    $response->assertSee('Total Siswa Aktif');
    $response->assertSee('1 Rombel Kelas Aktif');
    $response->assertSee('Rp 500.000');
    $response->assertSee('100%');
    $response->assertSee('Matematika Wajib');
    $response->assertSee('Dimas Wahyu, S.Pd');
    $response->assertSee('Ruang Bima');
    $response->assertSee('Rayhan Pratama');
    $response->assertSee('Semester Ganjil T.A. 2026/2027');
});

test('admin dashboard mengunduh rekap harian sebagai csv', function () {
    $response = $this->get(route('admin.dashboard', ['export' => 'csv']));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
