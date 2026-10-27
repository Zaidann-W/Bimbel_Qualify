<?php

namespace Database\Seeders;

use App\Models\StudySession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StudySessionSeeder extends Seeder
{
    /**
     * Seed detail sesi belajar untuk halaman "Detail Sesi Belajar" siswa.
     */
    public function run(): void
    {
        $studySession = StudySession::updateOrCreate(
            [
                'session_number' => 14,
                'category_badge' => '12 Super Intensif UTBK SNBT',
            ],
            [
                'title' => 'Kalkulus & Aplikasi Integral: Luas & Volume Benda Putar',
                'description' => 'Pendalaman teknik integral substitusi dan parsial untuk penyelesaian irisan kurva tertutup dan tipe pemodelan spesial UTBK SNBT.',
                'total_sessions' => 24,
                'date' => today(),
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'duration_minutes' => 90,
                'room_name' => 'Ruangan Eureka (Lantai 2)',
                'room_capacity' => 15,
                'room_facilities' => 'Smart Board + AC',
                'target_ptn' => 'Top 3 PTN',
                'tutor_name' => 'Kak Hendra Wijaya, M.Sc',
                'tutor_rating' => 4.96,
                'tutor_reviews_count' => 320,
                'rfid_checkin_start_time' => '16:15:00',
            ],
        );

        $materials = [
            [
                'title' => 'Modul #11: Aplikasi Integral',
                'file_type' => 'PDF',
                'file_size' => '4.2 MB',
                'metadata' => 'Edisi Cetak 2025',
                'file_path' => Storage::url('session-materials/modul-11-aplikasi-integral.pdf'),
            ],
            [
                'title' => 'Lembar Latihan Soal HOTS UTBK',
                'file_type' => 'PDF',
                'file_size' => '1.8 MB',
                'metadata' => '18 Butir Soal',
                'file_path' => Storage::url('session-materials/lembar-latihan-soal-hots-utbk.pdf'),
            ],
            [
                'title' => 'Slide Interaktif Smart Board Eureka',
                'file_type' => 'Slide',
                'file_size' => 'Akses Online',
                'metadata' => 'Live Anotasi saat sesi',
                'file_path' => Storage::url('session-materials/slide-interaktif-smart-board-eureka.html'),
            ],
        ];

        foreach ($materials as $material) {
            $studySession->materials()->updateOrCreate(
                ['title' => $material['title']],
                $material,
            );
        }
    }
}
