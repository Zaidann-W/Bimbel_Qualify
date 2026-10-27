<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    use HasFactory;

    protected $table = 'class_groups';

    protected $fillable = [
        'academic_period_id',
        'code',
        'education_level',
        'grade',
        'major',
        'curriculum',
        'capacity',
        'status',
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}
