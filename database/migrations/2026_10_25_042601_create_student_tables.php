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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
        $table->string('nis', 30)->nullable()->unique();
        $table->string('nisn', 20)->nullable()->unique();
        $table->string('full_name', 150);
        $table->string('nickname', 50)->nullable();
        $table->string('birth_place', 100)->nullable();
        $table->date('birth_date')->nullable();
        $table->string('email', 190)->nullable();
        $table->string('phone_wa', 20)->nullable();
        $table->text('address')->nullable();
        $table->string('school_name', 150)->nullable();
        $table->string('school_grade', 10)->nullable();
        $table->enum('education_level', ['SD', 'SMP', 'SMA'])->nullable()->index();
        $table->string('curriculum', 50)->nullable();
        $table->string('major', 30)->nullable(); // IPA / IPS
        $table->foreignId('photo_file_id')->nullable()->constrained('files')->nullOnDelete();
        $table->enum('status', ['aktif', 'cuti', 'nonaktif', 'lulus'])->default('aktif')->index();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('guardians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
        $table->string('name', 150);
        $table->string('occupation', 100)->nullable();
        $table->string('phone_wa', 20)->nullable();
        $table->string('email', 190)->nullable();
        $table->text('address')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('student_guardians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
        $table->enum('relationship', ['ayah', 'ibu', 'wali']);
        $table->boolean('is_primary_contact')->default(false);
        $table->boolean('is_financial_responsible')->default(false);
        $table->timestamps();
        $table->unique(['student_id', 'guardian_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('student_guardians');
    Schema::dropIfExists('guardians');
    Schema::dropIfExists('students');
}
};
