<?php

use App\Models\User;
use App\Services\DashboardService;
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
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subjectId = DB::table('subjects')->insertGetId([
        'code' => 'MTK',
        'name' => 'Matematika',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $studentId = DB::table('students')->insertGetId([
        'full_name' => 'Budi Santoso',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('qualifix_tickets')->insert([
        'ticket_number' => 'QF-0001',
        'student_id' => $studentId,
        'subject_id' => $subjectId,
        'class_group_id' => $groupId,
        'title' => 'Cara menghitung limit',
        'question' => "Kapan pakai l'Hopital?",
        'status' => 'menunggu_tentor',
        'submitted_at' => now()->subDay(),
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    DB::table('leave_requests')->insert([
        'student_id' => $studentId,
        'type' => 'sakit',
        'reason' => 'Demam',
        'status' => 'menunggu',
        'created_at' => now()->subHours(3),
        'updated_at' => now()->subHours(3),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $studentId,
        'class_group_id' => $groupId,
        'started_on' => now()->subMonth()->toDateString(),
        'status' => 'aktif',
        'created_at' => now()->subMonth(),
        'updated_at' => now()->subMonth(),
    ]);
});

test('recentActivities renders human times for leave, ticket and enrollment rows', function () {
    $activities = app(DashboardService::class)->recentActivities();

    expect($activities)->toHaveCount(3);
    expect($activities[0]['title'])->toContain('Permintaan izin sakit untuk Budi Santoso');
    expect($activities[0]['time'])->not->toBeNull();
    expect(array_keys($activities[0]))->toBe(['type', 'title', 'description', 'time', 'status']);
});

test('admin dashboard renders recent activities without error', function () {
    $response = $this->get('/admin');

    $response->assertOk();
});
