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
    Schema::create('files', function (Blueprint $table) {
        $table->id();
        $table->string('original_name');
        $table->string('path', 500);
        $table->string('mime_type', 100)->nullable();
        $table->unsignedBigInteger('size_bytes')->nullable();
        $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });

    Schema::create('staff_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
        $table->string('employee_number', 50)->nullable()->unique();
        $table->string('full_name_with_title', 200);
        $table->string('job_title', 100)->nullable();
        $table->string('education', 150)->nullable();
        $table->text('address')->nullable();
        $table->date('joined_on')->nullable();
        $table->enum('status', ['aktif', 'nonaktif', 'cuti'])->default('aktif');
        $table->timestamps();
    });

    Schema::create('branches', function (Blueprint $table) {
        $table->id();
        $table->string('name', 150);
        $table->text('address')->nullable();
        $table->string('phone', 20)->nullable();
        $table->foreignId('director_staff_id')->nullable()->constrained('staff_profiles')->nullOnDelete();
        $table->timestamps();
    });

    Schema::create('academic_periods', function (Blueprint $table) {
        $table->id();
        $table->string('academic_year', 9); // contoh: 2024/2025
        $table->enum('semester', ['ganjil', 'genap']);
        $table->date('starts_on');
        $table->date('ends_on');
        $table->boolean('is_active')->default(false);
        $table->timestamps();
        $table->unique(['academic_year', 'semester']);
    });

    Schema::create('branch_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('branch_id')->unique()->constrained()->cascadeOnDelete();
        $table->foreignId('active_academic_period_id')->nullable()->constrained('academic_periods')->nullOnDelete();
        $table->boolean('auto_payment_receipt_wa')->default(true);
        $table->boolean('auto_absence_wa')->default(true);
        $table->boolean('auto_due_invoice_wa')->default(true);
        $table->timestamps();
    });

    Schema::create('study_shifts', function (Blueprint $table) {
        $table->id();
        $table->string('name', 50); // sore / malam
        $table->time('starts_at');
        $table->time('ends_at');
        $table->timestamps();
    });

    Schema::create('subjects', function (Blueprint $table) {
        $table->id();
        $table->string('code', 20)->unique(); // MTK, FIS, ING
        $table->string('name', 100);
        $table->timestamps();
    });

    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100)->unique();
        $table->string('floor', 10)->nullable();
        $table->unsignedSmallInteger('capacity')->nullable();
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('rooms');
    Schema::dropIfExists('subjects');
    Schema::dropIfExists('study_shifts');
    Schema::dropIfExists('branch_settings');
    Schema::dropIfExists('academic_periods');
    Schema::dropIfExists('branches');
    Schema::dropIfExists('staff_profiles');
    Schema::dropIfExists('files');
}
};
