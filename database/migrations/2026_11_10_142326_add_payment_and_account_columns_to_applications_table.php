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
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('payment_status', ['belum_bayar', 'menunggu_verifikasi', 'lunas', 'ditolak'])
                ->default('belum_bayar')->after('status');
            $table->string('payment_proof_path')->nullable()->after('payment_status');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_proof_path');
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->nullOnDelete()
                ->after('payment_verified_at');
            $table->boolean('accounts_created')->default(false)->after('payment_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_proof_path',
                'payment_verified_at',
                'payment_verified_by',
                'accounts_created',
            ]);
        });
    }
};
