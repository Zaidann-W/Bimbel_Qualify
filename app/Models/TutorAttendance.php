<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorAttendance extends Model
{
    use HasFactory;

    protected $table = 'tutor_attendance';

    protected $fillable = [
        'class_session_id',
        'staff_id',
        'check_in_at',
        'check_out_at',
        'status',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class);
    }
}
