<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\ClassSession;
use App\Models\File;
use App\Models\Material;
use App\Models\QualifixTicket;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class SiswaDashboardDemoSeeder extends Seeder
{
    /**
     * Isi data contoh agar seluruh bagian dashboard siswa punya isi.
     */
    public function run(): void
    {
        $user = User::query()->where('role', 'siswa')->where('status', 'aktif')->orderBy('id')->first();

        if ($user === null) {
            $this->command->warn('Tidak ada akun siswa aktif. Jalankan RoleSeeder lebih dulu.');

            return;
        }

        $student = $user->student ?? Student::query()->whereNull('user_id')->orderBy('id')->first();

        if ($student === null) {
            $this->command->warn('Tidak ada data siswa untuk dihubungkan ke akun '.$user->email.'.');

            return;
        }

        if ($student->user_id === null) {
            $student->update(['user_id' => $user->id]);
        }

        if ($student->nickname === null) {
            $student->update(['nickname' => str($student->full_name)->explode(' ')->first()]);
        }

        $classGroupId = $student->enrollments()->where('status', 'aktif')->value('class_group_id');
        $subjectId = ClassSession::query()->whereNotNull('subject_id')->value('subject_id');
        $adminId = User::query()->where('role', 'admin')->value('id');

        if ($classGroupId === null || $subjectId === null) {
            $this->command->warn('Siswa belum punya kelas aktif atau mata pelajaran. Seeder dilewati.');

            return;
        }

        $this->seedModul($classGroupId, $subjectId, $adminId);
        $this->seedKlinik($student, $subjectId, $classGroupId);
        $this->seedPengumuman($classGroupId, $adminId);

        $this->command->info('Data demo dashboard siswa siap untuk '.$student->full_name.'.');
    }

    private function seedModul(int $classGroupId, int $subjectId, ?int $adminId): void
    {
        $daftar = [
            ['title' => 'Modul 08: Trik Cepat Integral & Limit Aljabar', 'description' => "Metode Huruf, L'Hopital, rumus cepat limit wajib.", 'size_bytes' => 4404019, 'hari' => 2],
            ['title' => 'Ringkasan Rumus Pater Fisika Modern', 'description' => 'Peta konsep interferensi, celah ganda, efek fotolistrik.', 'size_bytes' => 3250586, 'hari' => 4],
            ['title' => 'Bedah Soal Penalaran Matematika', 'description' => 'Kompilasi 45 soal edisi besar langkah demi langkah lengkap.', 'size_bytes' => 6081741, 'hari' => 6],
        ];

        foreach ($daftar as $item) {
            $berkas = File::query()->firstOrCreate(
                ['original_name' => $item['title'].'.pdf'],
                [
                    'path' => 'materials/'.str()->slug($item['title']).'.pdf',
                    'mime_type' => 'application/pdf',
                    'size_bytes' => $item['size_bytes'],
                    'uploaded_by' => $adminId,
                ],
            );

            $material = Material::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => $item['description'],
                    'subject_id' => $subjectId,
                    'file_id' => $berkas->id,
                    'uploaded_by' => $adminId,
                    'status' => 'terbit',
                    'allow_download' => true,
                    'published_at' => now()->subDays($item['hari']),
                ],
            );

            $material->classGroups()->syncWithoutDetaching([$classGroupId]);
        }
    }

    private function seedKlinik(Student $student, int $subjectId, int $classGroupId): void
    {
        $daftar = [
            ['ticket_number' => 'KPR-DEMO-0001', 'title' => 'Cara cepat limit bentuk 0/0', 'question' => 'Untuk soal limit aljabar bentuk 0/0, kapan pakai L\'Hopital dan kapan pakai pemfaktoran?', 'status' => 'selesai', 'resolved' => true],
            ['ticket_number' => 'KPR-DEMO-0002', 'title' => 'Beda substitusi dan parsial', 'question' => 'Bagaimana menentukan integral substitusi atau parsial saat soal tidak diberi petunjuk?', 'status' => 'menunggu_tentor', 'resolved' => false],
        ];

        foreach ($daftar as $item) {
            QualifixTicket::query()->updateOrCreate(
                ['ticket_number' => $item['ticket_number']],
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'class_group_id' => $classGroupId,
                    'title' => $item['title'],
                    'question' => $item['question'],
                    'status' => $item['status'],
                    'submitted_at' => now()->subDays(3),
                    'resolved_at' => $item['resolved'] ? now()->subDays(2) : null,
                ],
            );
        }
    }

    private function seedPengumuman(int $classGroupId, ?int $adminId): void
    {
        $sesi = ClassSession::query()
            ->where('class_group_id', $classGroupId)
            ->orderByDesc('session_date')
            ->first();

        Announcement::query()->updateOrCreate(
            ['title' => 'Pergantian Jam: Fisika Saintek (Kelas 12 IPA)'],
            [
                'body' => 'Jadwal selasa ketiga digeser dari pukul 16.00 – 17.00 WIB menjadi 15.30 – 17.00 WIB karena penyesuaian jadwal tentor.',
                'priority' => 'penting',
                'class_group_id' => $classGroupId,
                'class_session_id' => $sesi?->id,
                'room_id' => $sesi?->room_id,
                'published_at' => now(),
                'created_by' => $adminId,
            ],
        );
    }
}
