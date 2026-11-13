<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\File;
use App\Models\Material;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->actingAs($this->admin);

    $periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $kelasSma = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'status' => 'aktif',
    ]);

    $kelasSmp = ClassGroup::create([
        'academic_period_id' => $periode->id,
        'code' => '9-A',
        'education_level' => 'SMP',
        'grade' => '9',
        'status' => 'aktif',
    ]);

    $mapelMatematika = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
    $mapelFisika = Subject::create(['code' => 'FIS', 'name' => 'Fisika']);
    $mapelKimia = Subject::create(['code' => 'KIM', 'name' => 'Kimia']);

    $pengajar = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Ust. Ahmad Fauzi']);
    $pengajarLain = User::factory()->create(['role' => 'tentor', 'status' => 'aktif', 'name' => 'Ust. Budi Santoso']);

    $daftar = [
        ['Modul Matematika Intensif', 'terbit', [$kelasSma->id], $mapelMatematika->id, $pengajar->id],
        ['Modul Listrik Statis', 'draf', [$kelasSmp->id], $mapelFisika->id, $pengajar->id],
        ['Modul Praktikum Kimia', 'terbit', [$kelasSmp->id], $mapelKimia->id, $pengajarLain->id],
    ];

    foreach ($daftar as [$judul, $status, $classGroupIds, $subjectId, $penguploadId]) {
        $file = File::create([
            'original_name' => 'modul.pdf',
            'path' => 'materi/'.md5($judul).'.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1024,
        ]);

        $materi = Material::create([
            'title' => $judul,
            'subject_id' => $subjectId,
            'file_id' => $file->id,
            'uploaded_by' => $penguploadId,
            'status' => $status,
            'allow_download' => true,
            'published_at' => now(),
        ]);

        $materi->classGroups()->sync($classGroupIds);
    }
});

test('pencarian memfilter materi berdasarkan judul', function () {
    $this->get(route('admin.manajemenMateri', ['q' => 'Intensif']))
        ->assertStatus(200)
        ->assertSee('Modul Matematika Intensif')
        ->assertDontSee('Modul Listrik Statis')
        ->assertDontSee('Modul Praktikum Kimia');
});

test('pencarian memfilter materi berdasarkan mata pelajaran dan pengunggah', function () {
    $this->get(route('admin.manajemenMateri', ['q' => 'Fisika']))
        ->assertStatus(200)
        ->assertSee('Modul Listrik Statis')
        ->assertDontSee('Modul Matematika Intensif');

    $this->get(route('admin.manajemenMateri', ['q' => 'Ahmad Fauzi']))
        ->assertStatus(200)
        ->assertSee('Modul Matematika Intensif')
        ->assertSee('Modul Listrik Statis')
        ->assertDontSee('Modul Praktikum Kimia');
});

test('filter status hanya menampilkan materi sesuai statusnya', function () {
    $this->get(route('admin.manajemenMateri', ['status' => 'draf']))
        ->assertStatus(200)
        ->assertSee('Modul Listrik Statis')
        ->assertDontSee('Modul Matematika Intensif');

    $this->get(route('admin.manajemenMateri', ['status' => 'terbit']))
        ->assertStatus(200)
        ->assertSee('Modul Matematika Intensif')
        ->assertDontSee('Modul Listrik Statis');
});

test('tab jenjang hanya menampilkan materi untuk jenjang kelas sasaran', function () {
    $this->get(route('admin.manajemenMateri', ['jenjang' => 'smp']))
        ->assertStatus(200)
        ->assertSee('Modul Listrik Statis')
        ->assertSee('Modul Praktikum Kimia')
        ->assertDontSee('Modul Matematika Intensif');

    $this->get(route('admin.manajemenMateri', ['jenjang' => 'sma']))
        ->assertStatus(200)
        ->assertSee('Modul Matematika Intensif')
        ->assertDontSee('Modul Listrik Statis');
});

test('pencarian dan filter dapat digabung', function () {
    $this->get(route('admin.manajemenMateri', ['q' => 'Modul', 'status' => 'draf', 'jenjang' => 'smp']))
        ->assertStatus(200)
        ->assertSee('Modul Listrik Statis')
        ->assertDontSee('Modul Praktikum Kimia')
        ->assertDontSee('Modul Matematika Intensif');
});

test('filter yang dikosongkan mengembalikan seluruh daftar', function () {
    $this->get(route('admin.manajemenMateri', ['q' => 'Tidak Ada Hasil']))
        ->assertStatus(200)
        ->assertSee('Tidak ada materi yang cocok');

    $this->get(route('admin.manajemenMateri', ['q' => '', 'status' => 'semua']))
        ->assertStatus(200)
        ->assertSee('Modul Matematika Intensif')
        ->assertSee('Modul Listrik Statis')
        ->assertSee('Modul Praktikum Kimia');
});

test('jumlah pada tab jenjang mengikuti pencarian dan filter yang aktif', function () {
    $response = $this->get(route('admin.manajemenMateri', ['q' => 'Kimia']));

    $response->assertStatus(200);
    $response->assertSee('Modul Praktikum Kimia');
    $response->assertDontSee('Modul Matematika Intensif');

    $html = $response->getContent();

    // Setiap tab jenjang menghitung hanya materi yang cocok dengan pencarian.
    preg_match_all('/<a href="[^"]*jenjang=([a-z]+)"[^>]*>.*?<span[^>]*>(\d+)<\/span>/s', $html, $matches, PREG_SET_ORDER);

    $jumlah = [];

    foreach ($matches as $match) {
        $jumlah[$match[1]] = (int) $match[2];
    }

    expect($jumlah)->toMatchArray([
        'semua' => 1,
        'sma' => 0,
        'smp' => 1,
    ]);
});

test('filter aktif dapat dibersihkan lewat tombol hapus filter', function () {
    $this->get(route('admin.manajemenMateri', ['q' => 'Modul', 'status' => 'terbit']))
        ->assertStatus(200)
        ->assertSee('Hapus filter', false)
        ->assertSee(route('admin.manajemenMateri'), false);

    $this->get(route('admin.manajemenMateri'))
        ->assertStatus(200)
        ->assertDontSee('Hapus filter', false);
});

test('pilihan status dan jenjang terpilih pada form filter', function () {
    $this->get(route('admin.manajemenMateri', ['status' => 'draf', 'jenjang' => 'smp']))
        ->assertStatus(200)
        ->assertSee('<option value="draf" selected>', false)
        ->assertSee('name="jenjang" value="smp"', false);
});
