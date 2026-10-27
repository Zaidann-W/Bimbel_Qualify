<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_session_id')->constrained('study_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            $table->enum('attendance_status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Alpha');
            $table->timestamp('check_in_time')->nullable(); // Waktu tap RFID
            $table->text('permit_reason')->nullable(); // Alasan jika mengajukan formulir izin
            $table->enum('permit_status', ['Pending', 'Disetujui', 'Ditolak'])->nullable();
            $table->timestamps();

            $table->unique(['study_session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_attendances');
    }
};
