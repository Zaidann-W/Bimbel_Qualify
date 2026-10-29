<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materials';

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'file_id',
        'uploaded_by',
        'status',
        'allow_download',
        'published_at',
    ];

    protected $casts = [
        'allow_download' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function classGroups()
    {
        return $this->belongsToMany(ClassGroup::class, 'material_class_groups')
            ->withTimestamps();
    }
}
