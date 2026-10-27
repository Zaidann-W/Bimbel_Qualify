<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'registration_number',
        'batch_id',
        'source',
        'full_name',
        'nickname',
        'nisn',
        'birth_place',
        'birth_date',
        'school_name',
        'school_grade',
        'education_level',
        'curriculum',
        'major',
        'phone_wa',
        'email',
        'address',
        'guardian_name',
        'guardian_occupation',
        'guardian_phone',
        'group_wa',
        'study_shift_id',
        'status',
        'status_reason',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'student_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}