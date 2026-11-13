<?php

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->actingAs($this->admin);

    $periodId = DB::table('academic_periods')->insertGetId([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $classId = DB::table('class_groups')->insertGetId([
        'academic_period_id' => $periodId,
        'code' => '12-IPA-1',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->studentId = DB::table('students')->insertGetId([
        'full_name' => 'Aura Clarissa Putri',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('enrollments')->insert([
        'student_id' => $this->studentId,
        'class_group_id' => $classId,
        'started_on' => '2026-07-01',
        'status' => 'aktif',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->sessionId = DB::table('class_sessions')->insertGetId([
        'class_group_id' => $classId,
        'subject_id' => DB::table('subjects')->insertGetId([
            'code' => 'MTK',
            'name' => 'Matematika',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'staff_id' => DB::table('staff_profiles')->insertGetId([
            'user_id' => DB::table('users')->insertGetId([
                'name' => 'Dimas Wahyu',
                'email' => 'dimas@bimbel.test',
                'password' => bcrypt('rahasia'),
                'role' => 'tentor',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            'full_name_with_title' => 'Dimas Wahyu, S.Pd',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]),
        'session_date' => '2026-10-08',
        'start_time' => '15:30:00',
        'end_time' => '17:30:00',
        'status' => 'terjadwal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->leaveId = DB::table('leave_requests')->insertGetId([
        'student_id' => $this->studentId,
        'class_session_id' => $this->sessionId,
        'type' => 'sakit',
        'reason' => 'Demam',
        'status' => 'menunggu',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('admin bisa menyetujui pengajuan izin', function () {
    $this->patch(route('admin.izin.update', $this->leaveId), ['status' => 'disetujui'])
        ->assertRedirect();

    $leave = LeaveRequest::findOrFail($this->leaveId);

    expect($leave->status)->toBe('disetujui')
        ->and($leave->reviewed_by)->toBe($this->admin->id)
        ->and($leave->reviewed_at)->not->toBeNull();
});

test('izin yang disetujui langsung tercatat pada presensi siswa', function () {
    $this->patch(route('admin.izin.update', $this->leaveId), ['status' => 'disetujui']);

    $this->assertDatabaseHas('student_attendance', [
        'class_session_id' => $this->sessionId,
        'student_id' => $this->studentId,
        'status' => 'izin',
    ]);
});

test('admin bisa menolak pengajuan izin tanpa mencatat presensi', function () {
    $this->patch(route('admin.izin.update', $this->leaveId), ['status' => 'ditolak']);

    expect(LeaveRequest::findOrFail($this->leaveId)->status)->toBe('ditolak');

    $this->assertDatabaseMissing('student_attendance', [
        'class_session_id' => $this->sessionId,
        'student_id' => $this->studentId,
    ]);
});

test('status pengajuan di luar daftar yang diperbolehkan ditolak', function () {
    $this->patch(route('admin.izin.update', $this->leaveId), ['status' => 'diabaikan'])
        ->assertSessionHasErrors('status');

    expect(LeaveRequest::findOrFail($this->leaveId)->status)->toBe('menunggu');
});

test('pengajuan izin tidak bisa diputuskan oleh selain admin', function () {
    $this->actingAs(User::factory()->create(['role' => 'tentor', 'status' => 'aktif']));

    $this->patch(route('admin.izin.update', $this->leaveId), ['status' => 'disetujui'])
        ->assertRedirect(route('tentor.dashboard'));

    expect(LeaveRequest::findOrFail($this->leaveId)->status)->toBe('menunggu');
});
