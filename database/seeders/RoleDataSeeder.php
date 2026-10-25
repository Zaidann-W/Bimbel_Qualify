<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (Tentor, Siswa, Wali)
        $tentorUserId = DB::table('users')->insertGetId([
            'name' => 'Kak Hendra (Farhan Kamil, S.Si)',
            'email' => 'tentor@qualify.id',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $siswaUserId = DB::table('users')->insertGetId([
            'name' => 'Aisyah Putri Rahmadani',
            'email' => 'siswa@qualify.id',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $waliUserId = DB::table('users')->insertGetId([
            'name' => 'Bambang Rahmadani (Wali Aisyah)',
            'email' => 'wali@qualify.id',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Profil Tentor (Staff)
        $staffId = DB::table('staff_profiles')->insertGetId([
            'user_id' => $tentorUserId,
            'employee_number' => 'TTR-2024-001',
            'full_name_with_title' => 'Farhan Kamil, S.Si.',
            'job_title' => 'Master Tentor Matematika Saintek',
            'education' => 'S1 Matematika ITB',
            'status' => 'aktif',
            'joined_on' => '2023-01-15',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Periode Akademik
        $periodId = DB::table('academic_periods')->insertGetId([
            'academic_year' => '2025/2026',
            'semester' => 'ganjil',
            'starts_on' => '2025-07-01',
            'ends_on' => '2025-12-31',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Data Siswa
        $studentId = DB::table('students')->insertGetId([
            'user_id' => $siswaUserId,
            'nis' => 'QLF-2025-0101',
            'nisn' => '0067891234',
            'full_name' => 'Aisyah Putri Rahmadani',
            'nickname' => 'Aisyah',
            'school_name' => 'SMAN 3 Malang',
            'school_grade' => '12',
            'education_level' => 'SMA',
            'major' => 'IPA',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Data Wali / Orang Tua
        $guardianId = DB::table('guardians')->insertGetId([
            'user_id' => $waliUserId,
            'name' => 'Bambang Rahmadani',
            'occupation' => 'Wiraswasta',
            'phone_wa' => '081234567890',
            'email' => 'bambang.r@gmail.com',
            'address' => 'Jl. Ijen No. 45, Malang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_guardians')->insert([
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'relationship' => 'ayah',
            'is_primary_contact' => true,
            'is_financial_responsible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Data Tagihan (Invoices) - Role Wali
        $inv1 = DB::table('invoices')->insertGetId([
            'invoice_number' => 'INV-202504-0012',
            'student_id' => $studentId,
            'academic_period_id' => $periodId,
            'type' => 'spp_bulanan',
            'description' => 'SPP Bulan April 2025 - Program Super Intensif UTBK',
            'amount' => 1450000.00,
            'due_date' => '2025-04-10',
            'status' => 'belum_bayar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $inv2 = DB::table('invoices')->insertGetId([
            'invoice_number' => 'INV-202503-0008',
            'student_id' => $studentId,
            'academic_period_id' => $periodId,
            'type' => 'spp_bulanan',
            'description' => 'SPP Bulan Maret 2025 - Program Super Intensif UTBK',
            'amount' => 1450000.00,
            'due_date' => '2025-03-10',
            'status' => 'lunas',
            'paid_at' => '2025-03-08 14:20:00',
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        $inv3 = DB::table('invoices')->insertGetId([
            'invoice_number' => 'INV-202501-0001',
            'student_id' => $studentId,
            'academic_period_id' => $periodId,
            'type' => 'formulir_tes',
            'description' => 'Formulir Pendaftaran & Tes Minat Bakat SNBT',
            'amount' => 250000.00,
            'due_date' => '2025-01-20',
            'status' => 'lunas',
            'paid_at' => '2025-01-18 10:15:00',
            'created_at' => now()->subMonths(3),
            'updated_at' => now()->subMonths(3),
        ]);

        // 7. Data Pembayaran (Payments) - Role Wali
        DB::table('payments')->insert([
            'invoice_id' => $inv2,
            'payment_method' => 'va_bca',
            'amount' => 1450000.00,
            'transaction_ref' => 'VA-BCA-8891028190',
            'status' => 'berhasil',
            'paid_at' => '2025-03-08 14:20:00',
            'verified_at' => '2025-03-08 14:21:00',
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        DB::table('payments')->insert([
            'invoice_id' => $inv3,
            'payment_method' => 'qris',
            'amount' => 250000.00,
            'transaction_ref' => 'QRIS-NMID-99281726',
            'status' => 'berhasil',
            'paid_at' => '2025-01-18 10:15:00',
            'verified_at' => '2025-01-18 10:16:00',
            'created_at' => now()->subMonths(3),
            'updated_at' => now()->subMonths(3),
        ]);

        // 8. Ulasan Tentor (Tutor Reviews) - Role Siswa & Tentor
        DB::table('tutor_reviews')->insert([
            'staff_id' => $staffId,
            'student_id' => $studentId,
            'class_session_id' => null,
            'rating' => 5,
            'comment' => 'Penjelasan Kak Hendra sangat mudah dipahami! Trik cepat limit dan kalkulusnya ngebantu banget waktu tryout.',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);
    }
}
