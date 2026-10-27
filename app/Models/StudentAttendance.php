<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_attendance';

    protected $fillable = [
        'class_session_id',
        'student_id',
        'status',
        'arrived_at',
        'note',
        'recorded_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
