<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique(); // Contoh: INV-202504-0012
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_period_id')->nullable()->constrained('academic_periods')->nullOnDelete();
            $table->string('type', 50)->default('spp_bulanan'); // spp_bulanan, registrasi, formulir_tes, modul
            $table->string('description')->nullable(); // Contoh: SPP April 2025 - Kelas 12 IPA
            $table->decimal('amount', 12, 2); // Nominal tagihan
            $table->date('due_date')->nullable(); // Batas waktu bayar
            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas', 'batal'])->default('belum_bayar')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
