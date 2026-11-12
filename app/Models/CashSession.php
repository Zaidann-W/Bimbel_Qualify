<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'system_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Sesi kas yang masih berjalan.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
