<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    use HasFactory;

    protected $table = 'student_guardians';

    protected $fillable = [
        'student_id',
        'guardian_id',
        'relationship',
        'is_primary_contact',
        'is_financial_responsible',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'is_financial_responsible' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
