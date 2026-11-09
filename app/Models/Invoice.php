<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'gateway_callback_at' => 'datetime',
    ];

    /**
     * Scope untuk invoice yang tercatat di Xendit.
     */
    public function scopeHasXendit($query)
    {
        return $query->whereNotNull('xendit_invoice_id');
    }

    /**
     * Scope untuk invoice berdasarkan external_id Xendit.
     */
    public function scopeByExternalId($query, string $externalId)
    {
        return $query->where('external_id', $externalId);
    }

    /**
     * Tandai invoice telah dibayar melalui gateway Xendit.
     */
    public function markPaidViaXendit(string $xenditPaymentId, ?string $xenditStatus, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'lunas',
            'paid_at' => now(),
            'gateway_callback_at' => now(),
        ]);

        $this->payments()->create([
            'payment_method' => $gatewayResponse['payment_method'] ?? 'xendit',
            'amount' => $this->amount,
            'transaction_ref' => $xenditPaymentId,
            'xendit_payment_id' => $xenditPaymentId,
            'xendit_external_id' => $this->external_id,
            'xendit_status' => $xenditStatus,
            'status' => 'berhasil',
            'paid_at' => now(),
            'gateway_response' => $gatewayResponse,
            'xendit_callback_at' => now(),
        ]);
    }

    /**
     * Tandai invoice telah kedaluwarsa (EXPIRED) pada Xendit.
     */
    public function markExpiredViaXendit(?string $xenditPaymentId, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'batal',
            'gateway_callback_at' => now(),
        ]);

        $this->payments()->create([
            'payment_method' => $gatewayResponse['payment_method'] ?? 'xendit',
            'amount' => $this->amount,
            'transaction_ref' => $xenditPaymentId,
            'xendit_payment_id' => $xenditPaymentId,
            'xendit_external_id' => $this->external_id,
            'xendit_status' => 'EXPIRED',
            'status' => 'expired',
            'gateway_response' => $gatewayResponse,
            'xendit_callback_at' => now(),
        ]);
    }

    /**
     * Tandai invoice gagal (FAILED) pada Xendit.
     */
    public function markFailedViaXendit(?string $xenditPaymentId, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'batal',
            'gateway_callback_at' => now(),
        ]);

        $this->payments()->create([
            'payment_method' => $gatewayResponse['payment_method'] ?? 'xendit',
            'amount' => $this->amount,
            'transaction_ref' => $xenditPaymentId,
            'xendit_payment_id' => $xenditPaymentId,
            'xendit_external_id' => $this->external_id,
            'xendit_status' => 'FAILED',
            'status' => 'gagal',
            'gateway_response' => $gatewayResponse,
            'xendit_callback_at' => now(),
        ]);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
