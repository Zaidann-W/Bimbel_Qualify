<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\StudyShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RegistrationSupportSeeder extends Seeder
{
    /**
     * Seed the reference data required by the public registration form.
     *
     * The seeder is idempotent: every run refreshes the active academic period
     * and rolls the admission window forward to the current academic year, so a
     * re-seed never leaves the form stuck in a "pendaftaran ditutup" state.
     */
    public function run(): void
    {
        // The school year runs July to June.
        $academicYearStart = AcademicPeriod::currentYearStart();

        // 1. Study shifts: Sesi Sore and Sesi Malam
        StudyShift::firstOrCreate(['name' => 'Sesi Sore'], [
            'starts_at' => '15:30:00',
            'ends_at' => '17:30:00',
        ]);

        StudyShift::firstOrCreate(['name' => 'Sesi Malam'], [
            'starts_at' => '18:30:00',
            'ends_at' => '20:30:00',
        ]);

        // 2. Academic period covering today, set as the only active one
        $period = $this->refreshCurrentAcademicPeriod($academicYearStart);

        // 3. Single admission batch, open for the whole current academic year
        AdmissionBatch::updateOrCreate(['wave_number' => 1], [
            'academic_period_id' => $period->id,
            'name' => 'Gelombang 1',
            'opens_at' => Carbon::create($academicYearStart, 7, 1),
            'closes_at' => Carbon::create($academicYearStart + 1, 6, 30),
            'quota' => 100,
        ]);
    }

    /**
     * Create or refresh the academic period that contains today, and make it the
     * only active one so the dashboard and the registration form read the same
     * period.
     */
    private function refreshCurrentAcademicPeriod(int $academicYearStart): AcademicPeriod
    {
        $isFirstSemester = now()->year === $academicYearStart;

        $period = AcademicPeriod::updateOrCreate(
            [
                'academic_year' => $academicYearStart.'/'.($academicYearStart + 1),
                'semester' => $isFirstSemester ? 'ganjil' : 'genap',
            ],
            [
                'starts_on' => $isFirstSemester
                    ? Carbon::create($academicYearStart, 7, 1)
                    : Carbon::create($academicYearStart + 1, 1, 1),
                'ends_on' => $isFirstSemester
                    ? Carbon::create($academicYearStart, 12, 31)
                    : Carbon::create($academicYearStart + 1, 6, 30),
                'is_active' => true,
            ]
        );

        AcademicPeriod::where('id', '!=', $period->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return $period;
    }
}
