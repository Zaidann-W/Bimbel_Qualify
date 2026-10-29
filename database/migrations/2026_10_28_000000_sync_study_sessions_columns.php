<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selaraskan tabel study_sessions yang sudah terlanjur dibuat dengan skema lama
     * ke skema yang dipakai model StudySession dan halaman detail sesi siswa.
     */
    public function up(): void
    {
        Schema::table('study_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('study_sessions', 'title')) {
                $table->string('title')->nullable()->after('id');
            }

            if (! Schema::hasColumn('study_sessions', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'category_badge')) {
                $table->string('category_badge')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'session_number')) {
                $table->integer('session_number')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'total_sessions')) {
                $table->integer('total_sessions')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'date')) {
                $table->date('date')->nullable()->index();
            }

            if (! Schema::hasColumn('study_sessions', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(90);
            }

            if (! Schema::hasColumn('study_sessions', 'room_name')) {
                $table->string('room_name')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'room_capacity')) {
                $table->integer('room_capacity')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'room_facilities')) {
                $table->string('room_facilities')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'target_ptn')) {
                $table->string('target_ptn')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'tutor_name')) {
                $table->string('tutor_name')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'tutor_rating')) {
                $table->decimal('tutor_rating', 3, 2)->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'tutor_reviews_count')) {
                $table->integer('tutor_reviews_count')->nullable();
            }

            if (! Schema::hasColumn('study_sessions', 'rfid_checkin_start_time')) {
                $table->time('rfid_checkin_start_time')->nullable();
            }
        });

        // Kolom lama tidak dipakai lagi oleh halaman detail sesi, jadi dibuat opsional.
        Schema::table('study_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('study_sessions', 'name')) {
                $table->string('name')->nullable()->change();
            }

            if (Schema::hasColumn('study_sessions', 'day_of_week')) {
                $table->unsignedTinyInteger('day_of_week')->nullable()->change();
            }

            if (Schema::hasColumn('study_sessions', 'is_active')) {
                $table->boolean('is_active')->default(true)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_sessions', function (Blueprint $table) {
            foreach ([
                'title',
                'description',
                'category_badge',
                'session_number',
                'total_sessions',
                'date',
                'duration_minutes',
                'room_name',
                'room_capacity',
                'room_facilities',
                'target_ptn',
                'tutor_name',
                'tutor_rating',
                'tutor_reviews_count',
                'rfid_checkin_start_time',
            ] as $column) {
                if (Schema::hasColumn('study_sessions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
