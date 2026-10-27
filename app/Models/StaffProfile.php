<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    use HasFactory;

    protected $table = 'staff_profiles';

    protected $fillable = [
        'user_id',
        'employee_number',
        'full_name_with_title',
        'job_title',
        'education',
        'address',
        'joined_on',
        'status',
    ];

    protected $casts = [
        'joined_on' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}