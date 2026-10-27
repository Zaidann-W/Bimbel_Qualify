<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionAttendance extends Model
{
    use HasFactory;

    public const STATUS_HADIR = 'Hadir';

    public const STATUS_IZIN = 'Izin';

    public const STATUS_SAKIT = 'Sakit';

    public const STATUS_ALPHA = 'Alpha';

    public const PERMIT_PENDING = 'Pending';

    public const PERMIT_APPROVED = 'Disetujui';

    public const PERMIT_REJECTED = 'Ditolak';

    protected $table = 'session_attendances';

    protected $fillable = [
        'study_session_id',
        'student_id',
        'attendance_status',
        'check_in_time',
        'permit_reason',
        'permit_status',
    ];

    protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
        ];
    }

    public function studySession(): BelongsTo
    {
        return $this->belongsTo(StudySession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
