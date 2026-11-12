<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * Modul keuangan harian: akun kas, kategori pengeluaran, buku pengeluaran,
     * dan sesi kasir (buka/tutup kas).
     */
    public function up(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // contoh: Laci Kas (Fisik), Bank BCA
            $table->enum('type', ['tunai', 'bank', 'ewallet'])->default('tunai');
            $table->string('bank_name', 60)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('payment_methods', 250)->nullable(); // metode pembayaran yang menyetor ke akun ini, dipisah koma: va_bca,qris
            $table->decimal('opening_balance', 14, 2)->default(0); // saldo awal saat akun dibuat
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->decimal('opening_balance', 14, 2)->default(0); // kas awal saat buka
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('counted_balance', 14, 2)->nullable(); // hasil hitung fisik saat tutup kas
            $table->decimal('system_balance', 14, 2)->nullable(); // saldo menurut buku saat tutup kas
            $table->decimal('difference', 14, 2)->nullable(); // counted_balance - system_balance
            $table->string('note')->nullable();
            $table->enum('status', ['aktif', 'ditutup'])->default('aktif')->index();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamp('spent_at')->index(); // waktu pengeluaran tercatat
            $table->string('description');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receipt_file_id')->nullable()->constrained('files')->nullOnDelete(); // foto / PDF bukti
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('cash_sessions');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('cash_accounts');
    }
};
