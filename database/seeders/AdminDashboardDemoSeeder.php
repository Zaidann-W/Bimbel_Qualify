<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdminDashboardDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists (admin user from RoleSeeder)
        $adminExists = DB::table('users')->where('email', 'admin@qualify.test')->exists();

        if (!$adminExists) {
            // Admin user already exists from RoleSeeder, skip
            $this->command->info('Admin user already exists from RoleSeeder, skipping admin creation.');
        } else {
            $this->command->info('Admin user from RoleSeeder already present.');
        }

        // Create study shifts if not exist
        $shift1Exists = DB::table('study_shifts')->where('name', 'Sesi Sore')->exists();
        if (!$shift1Exists) {
            DB::table('study_shifts')->insert([
                ['name' => 'Sesi Sore', 'starts_at' => '15:30:00', 'ends_at' => '17:30:00', 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Study shift Sesi Sore created.');
        } else {
            $this->command->info('Study shift Sesi Sore already exists.');
        }

        $shift2Exists = DB::table('study_shifts')->where('name', 'Sesi Malam')->exists();
        if (!$shift2Exists) {
            DB::table('study_shifts')->insert([
                ['name' => 'Sesi Malam', 'starts_at' => '18:30:00', 'ends_at' => '20:30:00', 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Study shift Sesi Malam created.');
        } else {
            $this->command->info('Study shift Sesi Malam already exists.');
        }

        // Create academic period if not exist
        $periodExists = DB::table('academic_periods')->where('academic_year', '2025/2026')
            ->where('semester', 'ganjil')->exists();
        if (!$periodExists) {
            DB::table('academic_periods')->insert([
                ['academic_year' => '2025/2026', 'semester' => 'ganjil',
                    'starts_on' => '2025-07-01', 'ends_on' => '2025-12-31',
                    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $this->command->info('Academic period 2025/2026 ganjil created.');
        } else {
            $this->command->info('Academic period 2025/2026 ganjil already exists.');
        }

        // Get academic period ID
        $academicPeriod = DB::table('academic_periods')
            ->where('academic_year', '2025/2026')
            ->where('semester', 'ganjil')
            ->value('id');

        // Create admission batch if not exist
        if ($academicPeriod) {
            $batchExists = DB::table('admission_batches')
                ->where('name', 'Gelombang 1')
                ->where('wave_number', 1)
                ->exists();
            if (!$batchExists) {
                DB::table('admission_batches')->insert([
                    'name' => 'Gelombang 1',
                    'wave_number' => 1,
                    'academic_period_id' => $academicPeriod,
                    'opens_at' => '2025-01-01',
                    'closes_at' => '2025-12-31',
                    'quota' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('Admission batch Gelombang 1 created.');
            } else {
                $this->command->info('Admission batch Gelombang 1 already exists.');
            }
        }

        // Create subjects if not exist
        $mtkExists = DB::table('subjects')->where('code', 'MTK')->exists();
        if (!$mtkExists) {
            DB::table('subjects')->insert(['code' => 'MTK', 'name' => 'Matematika', 'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Subject MTK created.');
        } else {
            $this->command->info('Subject MTK already exists.');
        }

        $indoExists = DB::table('subjects')->where('code', 'IND')->exists();
        if (!$indoExists) {
            DB::table('subjects')->insert(['code' => 'IND', 'name' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Subject IND created.');
        } else {
            $this->command->info('Subject IND already exists.');
        }

        $ipaExists = DB::table('subjects')->where('code', 'IPA')->exists();
        if (!$ipaExists) {
            DB::table('subjects')->insert(['code' => 'IPA', 'name' => 'IPA', 'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Subject IPA created.');
        } else {
            $this->command->info('Subject IPA already exists.');
        }

        // Create rooms if not exist
        $room1Exists = DB::table('rooms')->where('name', 'Ruang 1')->exists();
        if (!$room1Exists) {
            DB::table('rooms')->insert(['name' => 'Ruang 1', 'floor' => '1', 'capacity' => 30, 'status' => 'aktif',
                'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Room Ruang 1 created.');
        } else {
            $this->command->info('Room Ruang 1 already exists.');
        }

        $room2Exists = DB::table('rooms')->where('name', 'Ruang 2')->exists();
        if (!$room2Exists) {
            DB::table('rooms')->insert(['name' => 'Ruang 2', 'floor' => '1', 'capacity' => 30, 'status' => 'aktif',
                'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Room Ruang 2 created.');
        } else {
            $this->command->info('Room Ruang 2 already exists.');
        }

        // Create class groups
        if ($academicPeriod) {
            $cg1Exists = DB::table('class_groups')
                ->where('academic_period_id', $academicPeriod)
                ->where('code', '10-IPA-1')->exists();
            if (!$cg1Exists) {
                DB::table('class_groups')->insert([
                    'academic_period_id' => $academicPeriod,
                    'code' => '10-IPA-1', 'education_level' => 'SMA', 'grade' => '10',
                    'major' => 'IPA', 'curriculum' => 'Kurikulum Merdeka',
                    'capacity' => 30, 'status' => 'aktif',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $this->command->info('Class group 10-IPA-1 created.');
            } else {
                $this->command->info('Class group 10-IPA-1 already exists.');
            }

            $cg2Exists = DB::table('class_groups')
                ->where('academic_period_id', $academicPeriod)
                ->where('code', '11-IPA-1')->exists();
            if (!$cg2Exists) {
                DB::table('class_groups')->insert([
                    'academic_period_id' => $academicPeriod,
                    'code' => '11-IPA-1', 'education_level' => 'SMA', 'grade' => '11',
                    'major' => 'IPA', 'curriculum' => 'Kurikulum Merdeka',
                    'capacity' => 30, 'status' => 'aktif',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $this->command->info('Class group 11-IPA-1 created.');
            } else {
                $this->command->info('Class group 11-IPA-1 already exists.');
            }
        }

        // Create tutor subjects - only if staff profile exists
        if ($academicPeriod) {
            $mathSubjectId = DB::table('subjects')->where('code', 'MTK')->value('id');
            // Check if staff_id=1 exists in staff_profiles before creating tutor_subject
            $staffExists = DB::table('staff_profiles')->where('id', 1)->exists();
            if ($staffExists) {
                $tsExists = DB::table('tutor_subjects')
                    ->where('staff_id', 1)
                    ->where('subject_id', $mathSubjectId)->exists();
                if (!$tsExists) {
                    DB::table('tutor_subjects')->insert(['staff_id' => 1, 'subject_id' => $mathSubjectId]);
                    $this->command->info('Tutor subject MTK created.');
                } else {
                    $this->command->info('Tutor subject MTK already exists.');
                }
            } else {
                $this->command->info('Staff profile id=1 not found, skipping tutor_subject creation.');
            }
        }

        $this->command->info('AdminDashboardDemoSeeder selesai dijalankan.');
        $this->command->info('Semua data dummy berhasil di-seed ke database.');
        $this->command->info('Akses dashboard: http://localhost:8000/admin dengan akun admin@qualify.test / password Qualify2025!');
    }
}