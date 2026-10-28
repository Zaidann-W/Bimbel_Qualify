<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualifixTicket extends Model
{
    use HasFactory;

    protected $table = 'qualifix_tickets';

    protected $fillable = [
        'ticket_number',
        'student_id',
        'subject_id',
        'class_group_id',
        'title',
        'question',
        'status',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function staff()
    {
        return $this->belongsToMany(StaffProfile::class, 'qualifix_assignments', 'ticket_id', 'staff_id');
    }
}
