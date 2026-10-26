<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorReview extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
