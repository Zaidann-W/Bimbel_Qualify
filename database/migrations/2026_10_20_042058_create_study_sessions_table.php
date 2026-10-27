<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: Kalkulus & Aplikasi Integral
            $table->text('description')->nullable(); // Deskripsi materi
            $table->string('category_badge')->nullable(); // Contoh: 12 Super Intensif UTBK SNBT
            $table->integer('session_number')->nullable(); // Contoh: Sesi ke-14
            $table->integer('total_sessions')->nullable(); // Contoh: dari 24 sesi
            $table->date('date')->index(); // Tanggal sesi
            $table->time('start_time'); // 15:30
            $table->time('end_time'); // 17:00
            $table->integer('duration_minutes')->default(90); // Durasi dalam menit
            $table->string('room_name'); // Ruangan Eureka (Lantai 2)
            $table->integer('room_capacity')->nullable(); // 15 siswa
            $table->string('room_facilities')->nullable(); // Smart Board + AC
            $table->string('target_ptn')->nullable(); // Target Top 3 PTN
            $table->string('tutor_name'); // Kak Hendra Wijaya, M.Sc
            $table->decimal('tutor_rating', 3, 2)->nullable(); // 4.96
            $table->integer('tutor_reviews_count')->nullable(); // 320 ulasan
            $table->time('rfid_checkin_start_time')->nullable(); // Dibuka mulai 16:15
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};
