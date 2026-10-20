<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('registrations', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();                    // kode pendaftaran publik
        $table->string('status')->default('SUBMITTED')->index();

        // data siswa
        $table->string('student_full_name');
        $table->string('student_nickname')->nullable();
        $table->date('student_birth_date');
        $table->string('grade');                             // kelas
        $table->string('academic_year')->nullable();         // hapus kalau "T.A" cukup jadi bagian grade
        $table->string('school_name');
        $table->string('student_phone', 20);
        $table->text('address');

        // data ortu
        $table->string('parent_name');
        $table->string('parent_phone', 20);
        $table->string('group_phone', 20)->nullable();       // hapus kalau selalu sama dengan nomor ortu/siswa

        // sesi belajar yang dipilih
        $table->foreignId('study_session_id')->constrained('study_sessions');

        // diisi nanti setelah akun siswa dibuat; FK ditambah saat tabel students ada
        $table->unsignedBigInteger('student_id')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('registrations');
}
};
