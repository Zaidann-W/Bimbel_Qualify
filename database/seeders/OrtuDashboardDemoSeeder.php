<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrtuDashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user ortu yang sudah ada (id=6: ortu@qualify.test), atau buat jika belum ada.
        $user = User::where('email', 'ortu@qualify.test')->first();
        if ($user === null) {
            $user = User::create([
                'name' => 'Ortu/Wali Siswa',
                'email' => 'ortu@qualify.test',
                'password' => Hash::make(RoleSeeder::DEFAULT_PASSWORD),
                'role' => 'ortu',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);
        }

        // 1. Data guardian (orang tua) terkait user ortu.
        // withTrashed() dipakai karena baris guardian yang pernah di-soft-delete
        // tetap tourner unique index pada kolom user_id.
        $guardian = Guardian::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Ortu/Wali Siswa',
                'occupation' => 'Wiraswasta',
                'phone_wa' => '081234567890',
                'email' => 'ortu@qualify.test',
                'address' => 'Jl. Contoh No. 12, Kota',
            ]
        );

        if ($guardian->trashed()) {
            $guardian->restore();
        }

        // 2. Referensi dasar yang diperlukan (jika belum ada).
        $period = AcademicPeriod::where('is_active', true)->first()
            ?? AcademicPeriod::create([
                'academic_year' => '2025/2026',
                'semester' => 'ganjil',
                'starts_on' => '2025-07-01',
                'ends_on' => '2025-12-31',
                'is_active' => true,
            ]);

        $subject = Subject::firstOrCreate(
            ['code' => 'MTK'],
            ['name' => 'Matematika']
        );
        $subjectFisika = Subject::firstOrCreate(
            ['code' => 'FIS'],
            ['name' => 'Fisika']
        );
        $subjectKimia = Subject::firstOrCreate(
            ['code' => 'KIM'],
            ['name' => 'Kimia']
        );

        $room = Room::firstOrCreate(
            ['name' => 'R. Eureka Lt. 2'],
            ['floor' => '2', 'capacity' => 20, 'status' => 'aktif']
        );
        $roomLab = Room::firstOrCreate(
            ['name' => 'Lab Newton Lt. 1'],
            ['floor' => '1', 'capacity' => 15, 'status' => 'aktif']
        );

        // Reuse akun tentor utama dari RoleSeeder agar Klinik PR punya satu profil staf tunggal.
        $staffUser = User::where('email', 'tentor@qualify.test')->first();
        if ($staffUser === null) {
            $staffUser = User::create([
                'name' => 'Tentor Utama',
                'email' => 'tentor@qualify.test',
                'password' => Hash::make(RoleSeeder::DEFAULT_PASSWORD),
                'role' => 'tentor',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);
        }

        $staff = StaffProfile::firstOrCreate(
            ['user_id' => $staffUser->id],
            [
                'employee_number' => 'TTR-TEST-001',
                'full_name_with_title' => 'Kak Hendra Wijaya, M.Sc',
                'job_title' => 'Master Tentor Matematika',
                'education' => 'S1 Matematika',
                'status' => 'aktif',
                'joined_on' => '2023-01-15',
            ]
        );

        // 3. Buat dua siswa yang menjadi anak dari guardian ini.
        // withTrashed() dipakai karena NIS yang sama pernah di-soft-delete
        // dan unique index pada kolom nis tetap aktif.
        $siswa1 = Student::withTrashed()->updateOrCreate(
            ['nis' => 'QLF-2025-0101'],
            [
                'user_id' => null,
                'nisn' => '0067891234',
                'full_name' => 'Aisyah Putri Azzahra',
                'nickname' => 'Aisyah',
                'school_name' => 'SMAN 3 Malang',
                'school_grade' => '12',
                'education_level' => 'SMA',
                'major' => 'IPA',
                'status' => 'aktif',
            ]
        );

        if ($siswa1->trashed()) {
            $siswa1->restore();
        }

        $siswa2 = Student::withTrashed()->updateOrCreate(
            ['nis' => 'QLF-2025-0102'],
            [
                'user_id' => null,
                'nisn' => '0098123499',
                'full_name' => 'Anindya Putri Rahayu',
                'nickname' => 'Anindya',
                'school_name' => 'SMAN 3 Malang',
                'school_grade' => '10',
                'education_level' => 'SMA',
                'major' => 'IPA',
                'status' => 'aktif',
            ]
        );

        if ($siswa2->trashed()) {
            $siswa2->restore();
        }

        // Hubungkan siswa ke guardian.
        DB::table('student_guardians')->updateOrInsert(
            ['student_id' => $siswa1->id, 'guardian_id' => $guardian->id],
            ['relationship' => 'ibu', 'is_primary_contact' => true, 'is_financial_responsible' => true]
        );
        DB::table('student_guardians')->updateOrInsert(
            ['student_id' => $siswa2->id, 'guardian_id' => $guardian->id],
            ['relationship' => 'ibu', 'is_primary_contact' => true, 'is_financial_responsible' => true]
        );

        // 4. Class group dan enrollment untuk tiap siswa.
        $kelas12 = ClassGroup::updateOrCreate(
            ['code' => '12-IPA-1', 'academic_period_id' => $period->id],
            [
                'education_level' => 'SMA',
                'grade' => '12',
                'major' => 'IPA',
                'curriculum' => 'Kurikulum Merdeka',
                'capacity' => 25,
                'status' => 'aktif',
            ]
        );
        $kelas10 = ClassGroup::updateOrCreate(
            ['code' => '10-IPA-1', 'academic_period_id' => $period->id],
            [
                'education_level' => 'SMA',
                'grade' => '10',
                'major' => 'IPA',
                'curriculum' => 'Kurikulum Merdeka',
                'capacity' => 25,
                'status' => 'aktif',
            ]
        );

        Enrollment::updateOrCreate(
            ['student_id' => $siswa1->id, 'class_group_id' => $kelas12->id],
            ['status' => 'aktif', 'started_on' => '2025-07-01']
        );
        Enrollment::updateOrCreate(
            ['student_id' => $siswa2->id, 'class_group_id' => $kelas10->id],
            ['status' => 'aktif', 'started_on' => '2025-07-01']
        );

        // 5. Jadwal kelas pekan ini (berdasarkan tanggal hari ini).
        $today = now()->copy()->startOfWeek();
        $hari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $jadwalData = [
            [1, 'Senin', $today->copy()->day(1), '15:30', '17:30', $kelas12->id, $subject->id, $staff->id, $room->id, 'Kalkulus & Integral Lanjutan'],
            [3, 'Rabu', $today->copy()->day(3), '15:30', '17:30', $kelas12->id, $subjectFisika->id, $staff->id, $roomLab->id, 'Fisika Modern & Mekanika Kuantum'],
            [4, 'Kamis', $today->copy()->day(4), '09:00', '10:30', $kelas10->id, $subjectKimia->id, $staff->id, $room->id, 'Kimia Organik & Stoikiometri Reaksi'],
            [6, 'Sabtu', $today->copy()->day(6), '08:00', '12:00', $kelas12->id, $subject->id, $staff->id, $room->id, 'Simulasi Try Out Akbar CBT SNBT'],
        ];

        foreach ($jadwalData as [$weekday, $namaHari, $tanggal, $mulai, $selesai, $kelasId, $mapelId, $tentorId, $ruangId, $topik]) {
            $session = ClassSession::updateOrCreate(
                [
                    'class_group_id' => $kelasId,
                    'session_date' => $tanggal,
                    'start_time' => $mulai,
                ],
                [
                    'subject_id' => $mapelId,
                    'staff_id' => $tentorId,
                    'room_id' => $ruangId,
                    'end_time' => $selesai,
                    'topic' => $topik,
                    'status' => 'terjadwal',
                ]
            );

            // Buat presensi untuk tiap siswa pada kelas yang relevan.
            $siswaTarget = $kelasId === $kelas12->id ? [$siswa1] : [$siswa2];
            foreach ($siswaTarget as $s) {
                $status = $this->randomStatus();
                StudentAttendance::updateOrCreate(
                    ['class_session_id' => $session->id, 'student_id' => $s->id],
                    [
                        'status' => $status,
                        'arrived_at' => $status === 'hadir'
                            ? $tanggal->copy()->setTimeFromTimeString($mulai)->subMinutes(5)
                            : null,
                        'note' => null,
                        'recorded_by' => null,
                    ]
                );
            }
        }

        // 6. Invoice dan pembayaran.
        $invA = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202504-0012'],
            [
                'student_id' => $siswa1->id,
                'academic_period_id' => $period->id,
                'type' => 'spp_bulanan',
                'description' => 'SPP Bulan April 2025 - Program Super Intensif UTBK',
                'amount' => 1450000.00,
                'due_date' => '2025-04-10',
                'status' => 'belum_bayar',
            ]
        );

        $invB = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202503-0008'],
            [
                'student_id' => $siswa1->id,
                'academic_period_id' => $period->id,
                'type' => 'spp_bulanan',
                'description' => 'SPP Bulan Maret 2025 - Program Super Intensif UTBK',
                'amount' => 1450000.00,
                'due_date' => '2025-03-10',
                'status' => 'lunas',
                'paid_at' => '2025-03-08 14:20:00',
            ]
        );

        $invC = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-202504-0013'],
            [
                'student_id' => $siswa2->id,
                'academic_period_id' => $period->id,
                'type' => 'spp_bulanan',
                'description' => 'SPP Bulan April 2025 - Kelas 10 IPA',
                'amount' => 1200000.00,
                'due_date' => '2025-04-10',
                'status' => 'lunas',
                'paid_at' => '2025-04-05 09:10:00',
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invB->id],
            [
                'payment_method' => 'va_bca',
                'amount' => 1450000.00,
                'transaction_ref' => 'VA-BCA-8891028190',
                'status' => 'berhasil',
                'paid_at' => '2025-03-08 14:20:00',
                'verified_at' => '2025-03-08 14:21:00',
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invC->id],
            [
                'payment_method' => 'qris',
                'amount' => 1200000.00,
                'transaction_ref' => 'QRIS-NMID-99281726',
                'status' => 'berhasil',
                'paid_at' => '2025-04-05 09:10:00',
                'verified_at' => '2025-04-05 09:11:00',
            ]
        );

        $this->command->info('OrtuDashboardDemoSeeder: data demo orang tua telah disiapkan.');
    }

    private function randomStatus(): string
    {
        $statuses = ['hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa'];

        return $statuses[array_rand($statuses)];
    }
}
