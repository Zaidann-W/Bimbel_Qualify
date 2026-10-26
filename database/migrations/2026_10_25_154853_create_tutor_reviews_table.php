<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_profiles')->cascadeOnDelete(); // Tentor
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete(); // Siswa
            $table->foreignId('class_session_id')->nullable()->constrained('class_sessions')->nullOnDelete(); // Sesi kelas
            $table->unsignedTinyInteger('rating'); // 1 sampai 5
            $table->text('comment')->nullable(); // Ulasan teks
            $table->timestamps();

            $table->unique(['student_id', 'staff_id', 'class_session_id'], 'student_tutor_session_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_reviews');
    }
};
