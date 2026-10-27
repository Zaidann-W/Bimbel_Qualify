<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'full_name',
        'nickname',
        'birth_place',
        'birth_date',
        'email',
        'phone_wa',
        'address',
        'school_name',
        'school_grade',
        'education_level',
        'curriculum',
        'major',
        'photo_file_id',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'photo_file_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}