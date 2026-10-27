<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $table = 'academic_periods';

    protected $fillable = [
        'academic_year',
        'semester',
        'starts_on',
        'ends_on',
        'is_active',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_active' => 'boolean',
    ];

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
