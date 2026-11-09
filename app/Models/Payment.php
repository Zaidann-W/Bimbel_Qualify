<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'gateway_response' => 'array',
        'xendit_callback_at' => 'datetime',
    ];

    /**
     * Scope untuk pembayaran yang berasal dari Xendit.
     */
    public function scopeHasXendit($query)
    {
        return $query->whereNotNull('xendit_payment_id');
    }

    /**
     * Scope untuk pembayaran berdasarkan xendit_payment_id.
     */
    public function scopeByXenditPaymentId($query, string $xenditPaymentId)
    {
        return $query->where('xendit_payment_id', $xenditPaymentId);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
