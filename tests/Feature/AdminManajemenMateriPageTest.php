<?php

use App\Models\AcademicPeriod;
use App\Models\ClassGroup;
use App\Models\File;
use App\Models\Material;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\MateriAjarReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->actingAs($this->admin);

    $this->periode = AcademicPeriod::create([
        'academic_year' => '2026/2027',
        'semester' => 'ganjil',
        'starts_on' => '2026-07-01',
        'ends_on' => '2026-12-31',
        'is_active' => true,
    ]);

    $this->kelas = ClassGroup::create([
        'academic_period_id' => $this->periode->id,
        'code' => '12-IPA-1',
        'education_level' => 'SMA',
        'grade' => '12',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $this->mapel = Subject::create(['code' => 'MTK', 'name' => 'Matematika Saintek']);
});

test('manajemen materi returns a successful response', function () {
    $response = $this->get('/admin/manajemen-materi');

    $response->assertStatus(200);
    $response->assertSee('Manajemen Materi Ajar');
    $response->assertSee('Kelola repositori materi pelajaran, modul ajar, dan distribusi berkas untuk kelas siswa bimbel QUALIFY.', false);
    $response->assertSee('Total Materi Ajar');
    $response->assertSee('Kelas Terdistribusi');
    $response->assertSee('Materi Diterbitkan');
    $response->assertSee('Unggah Materi Pelajaran');
});

test('manajemen materi is reachable from its named route', function () {
    $this->get(route('admin.manajemenMateri'))->assertStatus(200);
});

test('manajemen materi menu is active on the page', function () {
    $this->get(route('admin.manajemenMateri'))
        ->assertStatus(200)
        ->assertSee('Manajemen Materi Ajar');
});

test('manajemen materi lists repository files with access mode', function () {
    Storage::fake('local');

    $berkas = File::create([
        'original_name' => 'modul-matematika.pdf',
        'path' => 'materi/modul-matematika.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
        'uploaded_by' => $this->admin->id,
    ]);

    $materi = Material::create([
        'title' => 'Bank Soal TPS UTBK SNBT — Penalaran Matematika',
        'subject_id' => $this->mapel->id,
        'file_id' => $berkas->id,
        'uploaded_by' => $this->admin->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $materi->classGroups()->attach($this->kelas->id);

    $response = $this->get(route('admin.manajemenMateri'));

    $response->assertStatus(200);
    $response->assertSee('Bank Soal TPS UTBK SNBT — Penalaran Matematika');
    $response->assertSee('12-IPA-1');
    $response->assertSee('Boleh Unduh');
});

test('form unggah menampilkan pilihan mata pelajaran dan target kelas dari database', function () {
    $response = $this->get(route('admin.manajemenMateri'));

    $response->assertStatus(200);
    $response->assertSee('Matematika Saintek');
    $response->assertSee('12-IPA-1');
    $response->assertSee('value="'.$this->mapel->id.'"', false);
    $response->assertSee('value="'.$this->kelas->id.'"', false);
});

test('admin dapat menyimpan materi baru dengan kelas target dan hak akses unduhan', function () {
    Storage::fake('local');

    $response = $this->post(route('admin.manajemenMateri.store'), [
        'judul' => 'Modul 08: Trik Cepat Integral',
        'deskripsi' => 'Metode Huruf dan rumus cepat.',
        'berkas' => UploadedFile::fake()->create('modul-08.pdf', 120, 'application/pdf'),
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'akses' => '1',
    ]);

    $response->assertRedirect();

    $materi = Material::firstWhere('title', 'Modul 08: Trik Cepat Integral');

    expect($materi)->not->toBeNull()
        ->and($materi->subject_id)->toBe($this->mapel->id)
        ->and($materi->status)->toBe('terbit')
        ->and($materi->allow_download)->toBeTrue()
        ->and($materi->classGroups->pluck('id')->all())->toBe([$this->kelas->id]);

    Storage::disk('local')->assertExists($materi->file->path);
});

test('admin dapat menyimpan materi sebagai draf', function () {
    Storage::fake('local');

    $this->post(route('admin.manajemenMateri.store'), [
        'judul' => 'Draft Modul Fisika',
        'berkas' => UploadedFile::fake()->create('draft.pdf', 50, 'application/pdf'),
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'status' => 'draf',
    ])->assertRedirect();

    $materi = Material::firstWhere('title', 'Draft Modul Fisika');

    expect($materi)->not->toBeNull()
        ->and($materi->status)->toBe('draf')
        ->and($materi->published_at)->toBeNull();
});

test('halaman edit materi menampilkan data yang tersimpan', function () {
    $materi = Material::create([
        'title' => 'Modul Listrik Statis',
        'description' => 'Praktikum listrik statis.',
        'subject_id' => $this->mapel->id,
        'file_id' => File::create([
            'original_name' => 'modul.pdf',
            'path' => 'materi/modul.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 100,
        ])->id,
        'uploaded_by' => $this->admin->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $materi->classGroups()->attach($this->kelas->id);

    $response = $this->get(route('admin.manajemenMateri.edit', $materi));

    $response->assertStatus(200);
    $response->assertSee('Modul Listrik Statis');
    $response->assertSee('Praktikum listrik statis.');
    $response->assertSee('value="'.$this->kelas->id.'" checked', false);
    $response->assertSee('value="'.$this->mapel->id.'" selected', false);
});

test('admin dapat memperbarui materi dan mengganti berkas', function () {
    Storage::fake('local');

    $materi = Material::create([
        'title' => 'Judul Lama',
        'subject_id' => $this->mapel->id,
        'file_id' => File::create([
            'original_name' => 'lama.pdf',
            'path' => 'materi/lama.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 100,
        ])->id,
        'uploaded_by' => $this->admin->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $materi->classGroups()->attach($this->kelas->id);

    $this->put(route('admin.manajemenMateri.update', $materi), [
        'judul' => 'Judul Baru',
        'deskripsi' => 'Deskripsi baru.',
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'akses' => '1',
        'berkas' => UploadedFile::fake()->create('baru.pdf', 80, 'application/pdf'),
    ])->assertRedirect();

    $materi->refresh();

    expect($materi->title)->toBe('Judul Baru')
        ->and($materi->description)->toBe('Deskripsi baru.')
        ->and($materi->file->original_name)->toBe('baru.pdf');

    Storage::disk('local')->assertExists($materi->file->path);
});

test('daftar target kelas tersusun dari kelas 1 SD sampai kelas 12 SMA', function () {
    $this->seed(MateriAjarReferenceSeeder::class);

    $this->get(route('admin.manajemenMateri'))
        ->assertStatus(200)
        ->assertSeeInOrder([
            '1-A',
            '2-A',
            '3-A',
            '4-A',
            '5-A',
            '6-A',
            '7-A',
            '8-A',
            '9-A',
            '10-IPA-1',
            '10-IPA-2',
            '10-IPS-1',
            '11-IPA-1',
            '11-IPA-2',
            '11-IPS-1',
            '12-IPA-1',
            '12-IPA-2',
            '12-IPS-1',
            '12-IPS-2',
        ]);
});

test('target kelas memakai dropdown yang dapat dibuka dengan klik', function () {
    $response = $this->get(route('admin.manajemenMateri'));

    $response->assertStatus(200);
    $response->assertSee('materi-target-trigger', false);
    $response->assertSee('materi-target-panel', false);
    $response->assertSee('Pilih kelas');
    $response->assertSee('kelas dipilih');
});

test('notifikasi unggah tampil sebagai toast yang bisa ditutup dan hanya muncul sekali', function () {
    Storage::fake('local');

    $this->post(route('admin.manajemenMateri.store'), [
        'judul' => 'Modul Toast',
        'berkas' => UploadedFile::fake()->create('toast.pdf', 50, 'application/pdf'),
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'status' => 'terbit',
    ])->assertRedirect();

    $response = $this->get(route('admin.manajemenMateri'));

    $response->assertStatus(200);
    $response->assertSee('data-flash-toast', false);
    $response->assertSee('data-flash-close', false);
    $response->assertSee('berhasil diunggah');

    // Notifikasi tidak lagi muncul pada permintaan berikutnya.
    $this->get(route('admin.manajemenMateri'))
        ->assertDontSee('data-flash-toast', false);
});

test('status publikasi materi tidak berubah saat diedit', function () {
    Storage::fake('local');

    $materi = Material::create([
        'title' => 'Materi Status Terbit',
        'subject_id' => $this->mapel->id,
        'file_id' => File::create([
            'original_name' => 'status.pdf',
            'path' => 'materi/status.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 100,
        ])->id,
        'uploaded_by' => $this->admin->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $materi->classGroups()->attach($this->kelas->id);

    $this->put(route('admin.manajemenMateri.update', $materi), [
        'judul' => 'Materi Status Terbit',
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'akses' => '1',
    ])->assertRedirect();

    expect($materi->refresh()->status)->toBe('terbit');

    $materi->update(['status' => 'draf', 'published_at' => null]);

    $this->put(route('admin.manajemenMateri.update', $materi), [
        'judul' => 'Materi Status Draf',
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'akses' => '1',
    ])->assertRedirect();

    expect($materi->refresh()->status)->toBe('draf');
});

test('menyimpan perubahan materi mengarahkan kembali ke halaman daftar', function () {
    Storage::fake('local');

    $materi = Material::create([
        'title' => 'Judul Lama',
        'subject_id' => $this->mapel->id,
        'file_id' => File::create([
            'original_name' => 'lama.pdf',
            'path' => 'materi/lama.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 100,
        ])->id,
        'uploaded_by' => $this->admin->id,
        'status' => 'terbit',
        'allow_download' => true,
        'published_at' => now(),
    ]);

    $materi->classGroups()->attach($this->kelas->id);

    $response = $this->put(route('admin.manajemenMateri.update', $materi), [
        'judul' => 'nanang',
        'mapel' => $this->mapel->id,
        'target' => [$this->kelas->id],
        'akses' => '1',
    ]);

    $response->assertRedirect(route('admin.manajemenMateri'));
    $response->assertSessionHas('sukses');

    expect($materi->refresh()->title)->toBe('nanang');

    $this->get(route('admin.manajemenMateri'))
        ->assertStatus(200)
        ->assertSee('nanang');
});
