<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionBatch extends Model
{
    use HasFactory;

    protected $table = 'admission_batches';

    protected $fillable = [
        'academic_period_id',
        'name',
        'wave_number',
        'opens_at',
        'closes_at',
        'quota',
    ];

    protected $casts = [
        'opens_at' => 'date',
        'closes_at' => 'date',
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Gelombang yang jendela pendaftarannya terbuka pada hari ini.
     */
    public function scopeActive($query)
    {
        return $query->whereDate('opens_at', '<=', now())
            ->whereDate('closes_at', '>=', now())
            ->orderByDesc('wave_number')
            ->orderByDesc('opens_at');
    }

    /**
     * Gelombang pendaftaran yang sedang berjalan, jika ada.
     */
    public static function current(): ?self
    {
        return static::active()->with('academicPeriod')->first();
    }

    /**
     * Gelombang pendaftaran berikutnya yang belum dibuka, jika ada.
     */
    public static function next(): ?self
    {
        return static::whereDate('opens_at', '>', now())
            ->with('academicPeriod')
            ->orderBy('opens_at')
            ->first();
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'batch_id');
    }
}
