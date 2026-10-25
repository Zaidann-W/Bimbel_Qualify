<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('class_groups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_period_id')->constrained()->restrictOnDelete();
        $table->string('code', 30); // contoh: 12-IPA-1
        $table->enum('education_level', ['SD', 'SMP', 'SMA'])->nullable();
        $table->string('grade', 10)->nullable();
        $table->string('major', 30)->nullable();
        $table->string('curriculum', 50)->nullable();
        $table->unsignedSmallInteger('capacity')->nullable();
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->timestamps();
        $table->unique(['academic_period_id', 'code']);
    });

    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
        $table->date('started_on');
        $table->date('ended_on')->nullable();
        $table->enum('status', ['aktif', 'selesai', 'pindah', 'keluar'])->default('aktif');
        $table->timestamps();
        $table->index(['student_id', 'status']);
        $table->index(['class_group_id', 'status']);
    });

    Schema::create('tutor_subjects', function (Blueprint $table) {
        $table->id();
        $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['staff_id', 'subject_id']);
    });

    Schema::create('schedule_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained()->restrictOnDelete();
        $table->foreignId('staff_id')->constrained('staff_profiles')->restrictOnDelete();
        $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('study_shift_id')->nullable()->constrained()->nullOnDelete();
        $table->unsignedTinyInteger('weekday'); // 1=Senin ... 7=Minggu
        $table->time('start_time');
        $table->time('end_time');
        $table->timestamps();
    });

    Schema::create('class_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('schedule_rule_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained()->restrictOnDelete();
        $table->foreignId('staff_id')->constrained('staff_profiles')->restrictOnDelete();
        $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('study_shift_id')->nullable()->constrained()->nullOnDelete();
        $table->date('session_date');
        $table->time('start_time');
        $table->time('end_time');
        $table->string('topic')->nullable();
        $table->enum('status', ['terjadwal', 'berlangsung', 'selesai', 'dibatalkan'])->default('terjadwal');
        $table->timestamps();
        $table->index(['session_date', 'study_shift_id']);
        $table->index(['staff_id', 'session_date']);
        $table->index(['class_group_id', 'session_date']);
    });

    Schema::create('student_attendance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
        $table->timestamp('arrived_at')->nullable();
        $table->string('note')->nullable();
        $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        $table->unique(['class_session_id', 'student_id']);
        $table->index('student_id');
    });

    Schema::create('tutor_attendance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
        $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete();
        $table->timestamp('check_in_at')->nullable();
        $table->timestamp('check_out_at')->nullable();
        $table->enum('status', ['hadir', 'terlambat', 'tidak_hadir'])->default('hadir');
        $table->timestamps();
        $table->unique(['class_session_id', 'staff_id']);
    });

    Schema::create('leave_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->foreignId('class_session_id')->nullable()->constrained()->nullOnDelete();
        $table->string('type', 50); // sakit, izin lomba, dll
        $table->text('reason')->nullable();
        $table->foreignId('proof_file_id')->nullable()->constrained('files')->nullOnDelete();
        $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->index();
        $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('leave_requests');
    Schema::dropIfExists('tutor_attendance');
    Schema::dropIfExists('student_attendance');
    Schema::dropIfExists('class_sessions');
    Schema::dropIfExists('schedule_rules');
    Schema::dropIfExists('tutor_subjects');
    Schema::dropIfExists('enrollments');
    Schema::dropIfExists('class_groups');
}
};
