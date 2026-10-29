<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StudySession extends Model
{
    use HasFactory;

    protected $table = 'study_sessions';

    protected $fillable = [
        'title',
        'description',
        'category_badge',
        'session_number',
        'total_sessions',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'room_name',
        'room_capacity',
        'room_facilities',
        'target_ptn',
        'tutor_name',
        'tutor_rating',
        'tutor_reviews_count',
        'rfid_checkin_start_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'rfid_checkin_start_time' => 'datetime',
            'session_number' => 'integer',
            'total_sessions' => 'integer',
            'duration_minutes' => 'integer',
            'room_capacity' => 'integer',
            'tutor_rating' => 'float',
            'tutor_reviews_count' => 'integer',
        ];
    }

    /**
     * Apakah sesi sedang berjalan pada waktu ini (berlangsung hari ini dan sudah melewati jam mulai).
     */
    public function isRunning(): bool
    {
        return $this->date->isToday() && now()->between($this->start_time, $this->end_time);
    }

    /**
     * Apakah sesi telah selesai dilaksanakan.
     */
    public function isFinished(): bool
    {
        return $this->date->isPast() || ($this->date->isToday() && now()->greaterThan($this->end_time));
    }

    /**
     * Kunci status sesi untuk pemilihan warna badge: "berjalan", "terjadwal", atau "selesai".
     */
    protected function statusKey(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->isRunning()) {
                return 'berjalan';
            }

            return $this->isFinished() ? 'selesai' : 'terjadwal';
        });
    }

    /**
     * Label status sesi untuk badge, misalnya "Sesi Berjalan" atau "Terjadwal".
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->status_key) {
            'berjalan' => 'Sesi Berjalan',
            'selesai' => 'Selesai',
            default => 'Terjadwal',
        });
    }

    /**
     * Kelompok sesi berdasarkan jam mulai, misalnya "Sesi Sore" untuk sesi mulai 15.30.
     */
    protected function sessionSlotLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $hour = (int) $this->start_time->format('H');

            return match (true) {
                $hour < 10 => 'Sesi Pagi',
                $hour < 13 => 'Sesi Siang',
                $hour < 16 => 'Sesi Sore',
                default => 'Sesi Malam',
            };
        });
    }

    public function materials(): HasMany
    {
        return $this->hasMany(SessionMaterial::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(SessionAttendance::class);
    }

    /**
     * Label tanggal yang ramah untuk badge, misalnya "Hari Ini" atau "Senin, 10 Maret 2025".
     */
    protected function dateLabel(): Attribute
    {
        return Attribute::get(function (): string {
            return match (true) {
                $this->date->isToday() => 'Hari Ini',
                $this->date->isTomorrow() => 'Besok',
                $this->date->isYesterday() => 'Kemarin',
                default => $this->date->copy()->locale('id')->translatedFormat('l, j F Y'),
            };
        });
    }

    /**
     * Rentang waktu sesi dalam format Indonesia, misalnya "15.30 – 17.00 WIB".
     */
    protected function timeRangeLabel(): Attribute
    {
        return Attribute::get(fn (): string => sprintf(
            '%s – %s WIB',
            $this->start_time->format('H.i'),
            $this->end_time->format('H.i'),
        ));
    }

    /**
     * Waktu mulai presensi RFID, misalnya "16.15 WIB".
     */
    protected function rfidCheckinLabel(): Attribute
    {
        return Attribute::get(function (): ?string {
            $time = $this->rfid_checkin_start_time?->format('H.i');

            return $time !== null ? $time.' WIB' : null;
        });
    }

    /**
     * Inisial nama tentor untuk avatar, misalnya "HW" dari "Kak Hendra Wijaya, M.Sc".
     */
    protected function tutorInitials(): Attribute
    {
        return Attribute::get(function (): string {
            return Str::of($this->tutor_name)
                ->replaceMatches('/[.,]/', '')
                ->explode(' ')
                ->reject(fn (string $word): bool => in_array(Str::lower($word), ['kak', 'dr', 'drs', 'ir', 'prof'], true))
                ->filter()
                ->take(2)
                ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
                ->implode('');
        });
    }

    /**
     * Nama ruangan tanpa keterangan lantai, misalnya "Ruangan Eureka".
     */
    protected function roomBaseName(): Attribute
    {
        return Attribute::get(fn (): string => Str::of($this->room_name)
            ->replaceMatches('/\s*\([^)]*\)/', '')
            ->trim()
            ->toString());
    }

    /**
     * Keterangan lantai ruangan, misalnya "(Lantai 2)".
     */
    protected function roomFloorLabel(): Attribute
    {
        return Attribute::get(function (): ?string {
            preg_match('/\(([^)]+)\)/', $this->room_name, $matches);

            return isset($matches[1]) ? '('.$matches[1].')' : null;
        });
    }
}
