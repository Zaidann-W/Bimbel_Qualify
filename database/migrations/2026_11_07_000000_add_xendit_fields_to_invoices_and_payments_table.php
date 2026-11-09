<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('xendit_invoice_id', 100)->unique()->nullable()->index()->after('id');
            $table->text('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
            $table->string('external_id', 100)->unique()->nullable()->index()->after('xendit_invoice_url');
            $table->timestamp('expired_at')->nullable()->after('due_date');
            $table->string('payment_method', 50)->nullable()->after('status');
            $table->timestamp('gateway_callback_at')->nullable()->after('paid_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('xendit_payment_id', 100)->nullable()->index()->after('id');
            $table->string('xendit_external_id', 100)->nullable()->index()->after('xendit_payment_id');
            $table->string('xendit_status', 50)->nullable()->after('transaction_ref');
            $table->json('gateway_response')->nullable()->after('receipt_file_id');
            $table->timestamp('xendit_callback_at')->nullable()->after('gateway_response');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_invoice_id',
                'xendit_invoice_url',
                'external_id',
                'expired_at',
                'payment_method',
                'gateway_callback_at',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_payment_id',
                'xendit_external_id',
                'xendit_status',
                'gateway_response',
                'xendit_callback_at',
            ]);
        });
    }
};
