<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $table = 'application_documents';

    protected $fillable = [
        'application_id',
        'type',
        'file_id',
        'status',
        'review_note',
        'is_current',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
