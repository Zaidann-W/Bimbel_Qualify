<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionBatch extends Model
{
    use HasFactory;

    protected $table = 'admission_batches';

    protected $fillable = [
        'academic_period_id',
        'name',
        'wave_number',
        'opens_at',
        'closes_at',
        'quota',
    ];

    protected $casts = [
        'opens_at' => 'date',
        'closes_at' => 'date',
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'batch_id');
    }
}
