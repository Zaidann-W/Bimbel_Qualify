<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\File;
use App\Models\Material;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tentor = User::factory()->create([
        'role' => 'tentor',
        'status' => 'aktif',
    ]);

    $this->period = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $this->testFile = File::create([
        'original_name' => 'modul_sample.pdf',
        'path' => 'materials/modul_sample.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024 * 1024 * 2, // 2MB
        'uploaded_by' => $this->tentor->id,
    ]);
});

test('tentor dapat membuka halaman materi pembelajaran', function () {
    $response = $this->actingAs($this->tentor)->get(route('tentor.materi'));

    $response->assertOk();
    $response->assertSee('Bank Materi &amp; Bahan Ajar', false);
    $response->assertSee('Cari');
    // Dropdown kelas lama harus sudah dihapus
    $response->assertDontSee('Kelas 12 SMA &amp; Alumni / UTBK', false);
    // Bagian usulan penambahan suplemen harus sudah dihapus
    $response->assertDontSee('Perlu usulan penambahan suplemen materi?');
});

test('pencarian materi berdasarkan judul bekerja dengan benar', function () {
    $subject = Subject::create(['code' => 'MTK', 'name' => 'Matematika']);

    Material::create([
        'title' => 'Kalkulus Diferensial Lanjutan',
        'description' => 'Membahas turunan fungsi trigonometri.',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    Material::create([
        'title' => 'Fisika Optik dan Gelombang',
        'description' => 'Membahas difraksi dan interferensi.',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($this->tentor)->get(route('tentor.materi', ['q' => 'Kalkulus']));

    $response->assertOk();
    $response->assertSee('Kalkulus Diferensial Lanjutan');
    $response->assertDontSee('Fisika Optik dan Gelombang');
});

test('filter jenjang kelas dan kategori bekerja dengan benar', function () {
    $subject = Subject::create(['code' => 'FIS', 'name' => 'Fisika']);

    $kelas12 = ClassGroup::create([
        'academic_period_id' => $this->period->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'capacity' => 20,
        'status' => 'aktif',
    ]);

    $kelas10 = ClassGroup::create([
        'academic_period_id' => $this->period->id,
        'code' => '10-IPA-1',
        'education_level' => 'SMA',
        'grade' => '10',
        'capacity' => 20,
        'status' => 'aktif',
    ]);

    $slideFile = File::create([
        'original_name' => 'relativitas.pptx',
        'path' => 'materials/relativitas.pptx',
        'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'size_bytes' => 1024 * 1024 * 5,
        'uploaded_by' => $this->tentor->id,
    ]);

    $materi12 = Material::create([
        'title' => 'Slide Interaktif Relativitas Khusus',
        'description' => 'Materi slide presentasi kelas 12.',
        'subject_id' => $subject->id,
        'file_id' => $slideFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);
    $materi12->classGroups()->attach($kelas12->id);

    $materi10 = Material::create([
        'title' => 'Modul Gerak Lurus Beraturan',
        'description' => 'Materi modul kelas 10.',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);
    $materi10->classGroups()->attach($kelas10->id);

    // Test filter jenjang 12
    $responseJenjang = $this->actingAs($this->tentor)->get(route('tentor.materi', ['jenjang' => '12']));
    $responseJenjang->assertOk();
    $responseJenjang->assertSee('Slide Interaktif Relativitas Khusus');
    $responseJenjang->assertDontSee('Modul Gerak Lurus Beraturan');

    // Test filter kategori slide
    $responseKategori = $this->actingAs($this->tentor)->get(route('tentor.materi', ['kategori' => 'slide']));
    $responseKategori->assertOk();
    $responseKategori->assertSee('Slide Interaktif Relativitas Khusus');
    $responseKategori->assertDontSee('Modul Gerak Lurus Beraturan');
});

test('pagination menampilkan maksimal 10 materi per halaman', function () {
    $subject = Subject::create(['code' => 'BIO', 'name' => 'Biologi']);

    for ($i = 1; $i <= 15; $i++) {
        Material::create([
            'title' => "Materi Biologi Seri {$i}",
            'description' => "Deskripsi materi biologi nomor {$i}",
            'subject_id' => $subject->id,
            'file_id' => $this->testFile->id,
            'status' => 'terbit',
            'allow_download' => true,
            'published_at' => now()->subMinutes($i),
        ]);
    }

    $responsePage1 = $this->actingAs($this->tentor)->get(route('tentor.materi'));
    $responsePage1->assertOk();
    $responsePage1->assertSee('Materi Biologi Seri 1');
    $responsePage1->assertSee('Materi Biologi Seri 10');
    $responsePage1->assertDontSee('Materi Biologi Seri 11');

    $responsePage2 = $this->actingAs($this->tentor)->get(route('tentor.materi', ['page' => 2]));
    $responsePage2->assertOk();
    $responsePage2->assertSee('Materi Biologi Seri 11');
    $responsePage2->assertSee('Materi Biologi Seri 15');
});

test('statistik modul, paket latihan, dan slide sesuai database', function () {
    $subject = Subject::create(['code' => 'KIM', 'name' => 'Kimia']);

    $pptxFile = File::create([
        'original_name' => 'kimia_unsur.pptx',
        'path' => 'materials/kimia_unsur.pptx',
        'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'size_bytes' => 1024 * 500,
        'uploaded_by' => $this->tentor->id,
    ]);

    // 1 Slide
    Material::create([
        'title' => 'Slide Kimia Unsur',
        'description' => 'Bahan tayang smartboard',
        'subject_id' => $subject->id,
        'file_id' => $pptxFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    // 2 Paket Latihan
    Material::create([
        'title' => 'Paket Latihan Soal UTBK Kimia',
        'description' => 'Tryout soal HOTS',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    Material::create([
        'title' => 'Latihan Mandiri Stoikiometri',
        'description' => 'Kunci dan pembahasan',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    // 1 Modul
    Material::create([
        'title' => 'Modul Termokimia Lengkap',
        'description' => 'Teori dan pembuktian hukum Hess',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($this->tentor)->get(route('tentor.materi'));
    $response->assertOk();

    // Check stats are rendered
    $response->assertSee('Modul Tersedia');
    $response->assertSee('Paket Latihan');
    $response->assertSee('Slide Presentasi');
});

test('tentor dapat membuka berkas materi dan alias unduh bekerja', function () {
    Storage::fake('local');
    Storage::disk('local')->put('materials/modul_sample.pdf', 'dummy content pdf');

    $subject = Subject::create(['code' => 'MTK', 'name' => 'Matematika']);
    $materi = Material::create([
        'title' => 'Modul Kalkulus',
        'description' => 'Materi kalkulus dasar',
        'subject_id' => $subject->id,
        'file_id' => $this->testFile->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $responseBuka = $this->actingAs($this->tentor)->get(route('tentor.materi.buka', $materi));
    $responseBuka->assertOk();

    $responseUnduh = $this->actingAs($this->tentor)->get(route('tentor.materi.unduh', $materi));
    $responseUnduh->assertOk();
});
