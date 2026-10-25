<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('payment_method', 50); // qris, va_bca, transfer_bank, cash
            $table->decimal('amount', 12, 2); // Nominal yang dibayar
            $table->string('transaction_ref', 100)->nullable(); // Ref transaksi / No VA
            $table->enum('status', ['pending', 'berhasil', 'gagal', 'expired'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // Kasir / FO
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('receipt_file_id')->nullable()->constrained('files')->nullOnDelete(); // Kuitansi PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
