<?php

namespace App\Services;

use App\Models\StudyShift;
use Illuminate\Support\Carbon;

class PilihanPendaftaranService
{
    /**
     * Opsi kelas (jenjang + tingkat) yang dipakai pada formulir pendaftaran,
     * dipakai juga pada form data siswa agar keduanya selalu sinkron.
     *
     * @var array<string, array{label: string, jenjang: string, tingkat: string}>
     */
    public const KELAS = [
        'sd-4' => ['label' => 'SD Kelas 4', 'jenjang' => 'SD', 'tingkat' => '4'],
        'sd-5' => ['label' => 'SD Kelas 5', 'jenjang' => 'SD', 'tingkat' => '5'],
        'sd-6' => ['label' => 'SD Kelas 6', 'jenjang' => 'SD', 'tingkat' => '6'],
        'smp-7' => ['label' => 'SMP Kelas 7', 'jenjang' => 'SMP', 'tingkat' => '7'],
        'smp-8' => ['label' => 'SMP Kelas 8', 'jenjang' => 'SMP', 'tingkat' => '8'],
        'smp-9' => ['label' => 'SMP Kelas 9', 'jenjang' => 'SMP', 'tingkat' => '9'],
        'sma-10' => ['label' => 'SMA Kelas 10', 'jenjang' => 'SMA', 'tingkat' => '10'],
        'sma-11' => ['label' => 'SMA Kelas 11', 'jenjang' => 'SMA', 'tingkat' => '11'],
        'sma-12' => ['label' => 'SMA Kelas 12', 'jenjang' => 'SMA', 'tingkat' => '12'],
    ];

    /**
     * Kunci sesi belajar pada formulir pendaftaran.
     *
     * @var array<string, string>
     */
    public const SESI = [
        'sore' => 'Sesi Sore',
        'malam' => 'Sesi Malam',
    ];

    /**
     * Jam belajar untuk masing-masing sesi, dipakai saat data sesi belum ada.
     *
     * @var array<string, array{string, string}>
     */
    private const SESI_WAKTU = [
        'Sesi Sore' => ['15:30', '17:30'],
        'Sesi Malam' => ['18:30', '20:30'],
    ];

    /**
     * Study shift untuk nama sesi tertentu. Sesi standar dibuat otomatis
     * bila belum tersedia sehingga pilihan sesi selalu bisa dipakai.
     */
    private static function studyShift(string $nama): ?StudyShift
    {
        $waktu = self::SESI_WAKTU[$nama] ?? null;

        if ($waktu === null) {
            return StudyShift::where('name', $nama)->first();
        }

        return StudyShift::firstOrCreate(
            ['name' => $nama],
            ['starts_at' => $waktu[0], 'ends_at' => $waktu[1]],
        );
    }

    /**
     * Jenjang pendidikan dari nilai kelas (contoh: "sma-12" menjadi "SMA").
     */
    public static function jenjang(string $kelas): ?string
    {
        return self::KELAS[$kelas]['jenjang'] ?? null;
    }

    /**
     * Tingkat kelas dari nilai kelas (contoh: "sma-12" menjadi "12").
     */
    public static function tingkat(string $kelas): ?string
    {
        return self::KELAS[$kelas]['tingkat'] ?? null;
    }

    /**
     * Nilai kelas dari jenjang + tingkat, untuk mengisi select pada form admin.
     */
    public static function kelasDari(?string $jenjang, ?string $tingkat): ?string
    {
        if ($jenjang === null || $tingkat === null || $tingkat === '') {
            return null;
        }

        foreach (self::KELAS as $nilai => $opsi) {
            if ($opsi['jenjang'] === $jenjang && $opsi['tingkat'] === (string) $tingkat) {
                return $nilai;
            }
        }

        return null;
    }

    /**
     * Opsi kelas yang sudah dikelompokkan per jenjang untuk select form.
     *
     * @return array<string, array<string, string>>
     */
    public static function opsiKelas(): array
    {
        $opsi = [];

        foreach (self::KELAS as $nilai => $data) {
            $opsi[$data['jenjang']][$nilai] = $data['label'];
        }

        return $opsi;
    }

    /**
     * Id study shift untuk kunci sesi belajar.
     */
    public static function studyShiftId(string $sesi): ?int
    {
        $nama = self::SESI[$sesi] ?? $sesi;

        return self::studyShift($nama)?->id;
    }

    /**
     * Kunci sesi belajar dari id study shift.
     */
    public static function sesiDariStudyShift(?int $studyShiftId): ?string
    {
        if ($studyShiftId === null) {
            return null;
        }

        $nama = StudyShift::whereKey($studyShiftId)->value('name');

        if ($nama === null) {
            return null;
        }

        $terbalik = array_flip(self::SESI);

        return $terbalik[$nama] ?? null;
    }

    /**
     * Opsi sesi belajar lengkap dengan jamnya, untuk radio button pada form.
     *
     * @return array<string, array{label: string, waktu: string}>
     */
    public static function opsiSesi(): array
    {
        $opsi = [];

        foreach (self::SESI as $kunci => $nama) {
            $shift = self::studyShift($nama);

            $opsi[$kunci] = [
                'label' => $shift?->name ?? $nama,
                'waktu' => $shift
                    ? Carbon::parse($shift->starts_at)->format('H.i').' - '.Carbon::parse($shift->ends_at)->format('H.i').' WIB'
                    : '',
            ];
        }

        return $opsi;
    }
}
