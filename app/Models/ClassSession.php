<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $table = 'class_sessions';

    protected $fillable = [
        'schedule_rule_id',
        'class_group_id',
        'subject_id',
        'staff_id',
        'room_id',
        'study_shift_id',
        'session_date',
        'start_time',
        'end_time',
        'topic',
        'status',
    ];

    public function scheduleRule()
    {
        return $this->belongsTo(ScheduleRule::class, 'schedule_rule_id');
    }

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function studyShift()
    {
        return $this->belongsTo(StudyShift::class, 'study_shift_id');
    }

    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
    }
}
