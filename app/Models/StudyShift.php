<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyShift extends Model
{
    use HasFactory;

    protected $table = 'study_shifts';

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
    ];

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}
