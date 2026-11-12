<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashAccount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Daftar metode pembayaran yang menyetor ke akun ini (dari kolom payment_methods).
     *
     * @return array<int, string>
     */
    public function paymentMethods(): array
    {
        return array_values(array_filter(array_map(
            'trim',
            explode(',', (string) $this->payment_methods)
        )));
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function cashSessions(): HasMany
    {
        return $this->hasMany(CashSession::class);
    }

    /**
     * Akun kas yang masih dipakai untuk pencatatan.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
