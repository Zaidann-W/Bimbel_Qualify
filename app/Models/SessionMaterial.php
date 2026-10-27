<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SessionMaterial extends Model
{
    use HasFactory;

    protected $table = 'session_materials';

    protected $fillable = [
        'study_session_id',
        'title',
        'file_type',
        'file_size',
        'metadata',
        'file_path',
    ];

    public function studySession(): BelongsTo
    {
        return $this->belongsTo(StudySession::class);
    }

    /**
     * Materi berupa slide atau akses online dibuka langsung, bukan diunduh.
     */
    protected function isOnline(): Attribute
    {
        return Attribute::get(fn (): bool => in_array(
            Str::lower($this->file_type),
            ['slide', 'online', 'video'],
            true,
        ));
    }

    /**
     * Ringkasan format berkas untuk ditampilkan di daftar materi.
     */
    protected function detailLabel(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->is_online) {
                return collect([$this->file_size ?? 'Akses Online', $this->metadata])
                    ->filter()
                    ->implode(' • ');
            }

            return collect(['Format '.$this->file_type, $this->file_size, $this->metadata])
                ->filter()
                ->implode(' • ');
        });
    }
}
