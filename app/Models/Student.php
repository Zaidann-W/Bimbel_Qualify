<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardians', 'student_id', 'guardian_id')
            ->withPivot('relationship', 'is_primary_contact', 'is_financial_responsible')
            ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Dapatkan inisial nama siswa (huruf pertama nama depan dan belakang).
     */
    public function inisial(): string
    {
        $nama = trim($this->full_name ?? '');
        $pecah = preg_split('/\s+/', $nama);

        if (count($pecah) === 1) {
            return strtoupper(mb_substr($nama, 0, 1));
        }

        return strtoupper(mb_substr($pecah[0], 0, 1).mb_substr(end($pecah), 0, 1));
    }
}
