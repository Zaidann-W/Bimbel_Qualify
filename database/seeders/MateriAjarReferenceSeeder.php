<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class MateriAjarReferenceSeeder extends Seeder
{
    /**
     * Mata pelajaran standar yang dipakai bimbel untuk jenjang SNBT.
     *
     * @var list<array{0: string, 1: string}>
     */
    private const SUBJECTS = [
        ['MTK', 'Matematika'],
        ['FIS', 'Fisika'],
        ['KIM', 'Kimia'],
        ['BIO', 'Biologi'],
        ['EKO', 'Ekonomi'],
        ['SOS', 'Sosiologi'],
        ['GEO', 'Geografi'],
        ['BIND', 'Bahasa Indonesia'],
        ['BING', 'Bahasa Inggris'],
        ['PPKN', 'PPKn'],
        ['SEJ', 'Sejarah'],
        ['TPS', 'TPS'],
    ];

    /**
     * Daftar rombel lengkap: SD kelas 1–6, SMP kelas 7–9, SMA kelas 10–12.
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string|null}>
     */
    private const CLASSES = [
        ['1-A', 'SD', '1', null],
        ['2-A', 'SD', '2', null],
        ['3-A', 'SD', '3', null],
        ['4-A', 'SD', '4', null],
        ['5-A', 'SD', '5', null],
        ['6-A', 'SD', '6', null],
        ['7-A', 'SMP', '7', null],
        ['8-A', 'SMP', '8', null],
        ['9-A', 'SMP', '9', null],
        ['10-IPA-1', 'SMA', '10', 'IPA'],
        ['10-IPA-2', 'SMA', '10', 'IPA'],
        ['10-IPS-1', 'SMA', '10', 'IPS'],
        ['11-IPA-1', 'SMA', '11', 'IPA'],
        ['11-IPA-2', 'SMA', '11', 'IPA'],
        ['11-IPS-1', 'SMA', '11', 'IPS'],
        ['12-IPA-1', 'SMA', '12', 'IPA'],
        ['12-IPA-2', 'SMA', '12', 'IPA'],
        ['12-IPS-1', 'SMA', '12', 'IPS'],
        ['12-IPS-2', 'SMA', '12', 'IPS'],
    ];

    public function run(): void
    {
        foreach (self::SUBJECTS as [$code, $name]) {
            Subject::firstOrCreate(['code' => $code], ['name' => $name]);
        }

        $periode = $this->periodeAktif();

        foreach (self::CLASSES as [$code, $jenjang, $grade, $major]) {
            ClassGroup::updateOrCreate(
                [
                    'academic_period_id' => $periode->id,
                    'code' => $code,
                ],
                [
                    'education_level' => $jenjang,
                    'grade' => $grade,
                    'major' => $major,
                    'curriculum' => 'Kurikulum Merdeka',
                    'capacity' => 25,
                    'status' => 'aktif',
                ]
            );
        }
    }

    /**
     * Periode akademik yang sedang berjalan, dibuat otomatis bila belum ada.
     */
    private function periodeAktif(): AcademicPeriod
    {
        $periode = AcademicPeriod::where('is_active', true)->first();

        if ($periode !== null) {
            return $periode;
        }

        $tahun = (int) now()->format('Y');
        $awal = now()->month >= 7 ? $tahun : $tahun - 1;

        return AcademicPeriod::updateOrCreate(
            [
                'academic_year' => $awal.'/'.($awal + 1),
                'semester' => 'ganjil',
            ],
            [
                'starts_on' => $awal.'-07-01',
                'ends_on' => ($awal + 1).'-06-30',
                'is_active' => true,
            ]
        );
    }
}
