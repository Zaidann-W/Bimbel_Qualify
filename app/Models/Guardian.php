<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guardians';

    protected $fillable = [
        'user_id',
        'name',
        'occupation',
        'phone_wa',
        'email',
        'address',
    ];

    protected $casts = [
        'is_financial_responsible' => 'boolean',
        'is_primary_contact' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_guardians', 'guardian_id', 'student_id')
            ->withPivot('relationship', 'is_primary_contact', 'is_financial_responsible')
            ->withTimestamps();
    }
}
