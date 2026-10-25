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
    Schema::create('admission_batches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_period_id')->constrained()->restrictOnDelete();
        $table->string('name', 100);
        $table->unsignedTinyInteger('wave_number')->default(1);
        $table->date('opens_at')->nullable();
        $table->date('closes_at')->nullable();
        $table->unsignedSmallInteger('quota')->nullable();
        $table->timestamps();
    });

    Schema::create('applications', function (Blueprint $table) {
        $table->id();
        $table->string('registration_number', 30)->unique();
        $table->foreignId('batch_id')->nullable()->constrained('admission_batches')->nullOnDelete();
        $table->enum('source', ['online', 'manual'])->default('online');

        // data calon siswa
        $table->string('full_name', 150);
        $table->string('nickname', 50)->nullable();
        $table->string('nisn', 20)->nullable();
        $table->string('birth_place', 100)->nullable();
        $table->date('birth_date')->nullable();
        $table->string('school_name', 150)->nullable();
        $table->string('school_grade', 10)->nullable();
        $table->enum('education_level', ['SD', 'SMP', 'SMA'])->nullable();
        $table->string('curriculum', 50)->nullable();
        $table->string('major', 30)->nullable();
        $table->string('phone_wa', 20)->nullable();
        $table->string('email', 190)->nullable();
        $table->text('address')->nullable();

        // data wali
        $table->string('guardian_name', 150)->nullable();
        $table->string('guardian_occupation', 100)->nullable();
        $table->string('guardian_phone', 20)->nullable();
        $table->string('group_wa', 20)->nullable();
        $table->foreignId('study_shift_id')->nullable()->constrained('study_shifts')->nullOnDelete();

        // proses
        $table->enum('status', ['menunggu_review', 'perlu_perbaikan', 'diterima', 'ditolak', 'batal'])
              ->default('menunggu_review')->index();
        $table->text('status_reason')->nullable();
        $table->timestamp('submitted_at')->nullable()->useCurrent();
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        $table->foreignId('student_id')->nullable()->unique()->constrained('students')->nullOnDelete();
        $table->timestamps();
    });

    Schema::create('application_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('application_id')->constrained()->cascadeOnDelete();
        $table->string('type', 30); // pas_foto, rapor, kk_ktp, surat_komitmen
        $table->foreignId('file_id')->constrained('files')->restrictOnDelete();
        $table->string('status', 30)->default('menunggu');
        $table->text('review_note')->nullable();
        $table->boolean('is_current')->default(true);
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        $table->timestamps();
        $table->index(['application_id', 'type']);
    });

    Schema::create('application_status_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('application_id')->constrained()->cascadeOnDelete();
        $table->string('old_status', 30)->nullable();
        $table->string('new_status', 30);
        $table->text('reason')->nullable();
        $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('application_status_histories');
    Schema::dropIfExists('application_documents');
    Schema::dropIfExists('applications');
    Schema::dropIfExists('admission_batches');
}
};
