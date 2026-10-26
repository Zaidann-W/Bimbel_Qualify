<?php

namespace Database\Seeders;

use App\Models\AdmissionBatch;
use App\Models\AcademicPeriod;
use App\Models\StudyShift;
use Illuminate\Database\Seeder;

class RegistrationSupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Study shifts: Sesi Sore and Sesi Malam
        StudyShift::insert([
            [
                'name' => 'Sesi Sore',
                'starts_at' => '15:30:00',
                'ends_at' => '17:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sesi Malam',
                'starts_at' => '18:30:00',
                'ends_at' => '20:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. One active academic period
        AcademicPeriod::insert([
            [
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
                'starts_on' => '2025-07-01',
                'ends_on' => '2025-12-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. One active admission batch
        AdmissionBatch::insert([
            [
                'name' => 'Gelombang 1',
                'wave_number' => 1,
                'academic_period_id' => 1,
                'opens_at' => '2025-01-01',
                'closes_at' => '2025-12-31',
                'quota' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Note: Jam di atas bersifat sementara dengan catatan menunggu konfirmasi klien.
        // Nilai waktu ini bisa disesuaikan setelah konfirmasi dari klien.
    }
}