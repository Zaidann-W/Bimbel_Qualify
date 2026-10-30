<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Scope materi yang sudah diterbitkan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'terbit');
    }

    /**
     * Scope materi berdasarkan jenjang (education_level) kelas sasaran.
     */
    public function scopeForJenjang($query, string $jenjang)
    {
        return $query->whereHas('classGroups', function ($q) use ($jenjang) {
            $q->where('education_level', strtoupper($jenjang));
        });
    }

    /**
     * Label akses berdasarkan flag allow_download dari DB.
     */
    public function getAccessLabelAttribute(): string
    {
        return $this->allow_download ? 'Boleh Unduh' : 'Hanya Baca';
    }

    /**
     * Kelas CSS badge akses berdasarkan flag allow_download dari DB.
     */
    public function getAccessClassAttribute(): string
    {
        return $this->allow_download ? 'bg-sand text-muted' : 'bg-sandgold text-bronze';
    }

    /**
     * Tipe berkas (PDF / PPTX / DOCX) dari mime_type berkas terkait.
     */
    public function getFileTypeAttribute(): string
    {
        $mime = optional($this->file)->mime_type ?? '';

        return match (true) {
            str_contains($mime, 'pdf') => 'PDF',
            str_contains($mime, 'presentation') => 'PPTX',
            str_contains($mime, 'word') || str_contains($mime, 'document') => 'DOCX',
            default => strtoupper(pathinfo(optional($this->file)->original_name ?? 'berkas', PATHINFO_EXTENSION) ?: 'BERKAS'),
        };
    }

    /**
     * Nama ikon berkas berdasarkan tipe berkas dari DB.
     */
    public function getFileIconAttribute(): string
    {
        return match ($this->file_type) {
            'PDF' => 'file-text',
            'PPTX' => 'presentation',
            'DOCX' => 'file',
            default => 'file',
        };
    }

    /**
     * Ukuran berkas diformat (KB / MB) dari size_bytes di DB.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = optional($this->file)->size_bytes ?? 0;

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 1).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    /**
     * Tanggal unggah diformat (Indonesia) dari created_at di DB.
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->locale('id')->format('d M Y');
    }

    /**
     * Kode kelas sasaran digabung, diambil dari relasi class_groups di DB.
     */
    public function getClassCodesAttribute(): string
    {
        return $this->classGroups->pluck('code')->implode(', ');
    }

    /**
     * Daftar jenjang kelas sasaran untuk filter tab, diambil dari DB.
     */
    public function getJenjangListAttribute(): array
    {
        return $this->classGroups->pluck('education_level')->map(fn ($e) => strtolower($e))->unique()->values()->all();
    }
}
