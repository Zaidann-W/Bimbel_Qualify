<?php

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));

    pendaftaranTabApplication('REG-TAB-0001', 'Rayhan Pratama', 'menunggu_review');
    pendaftaranTabApplication('REG-TAB-0002', 'Dewi Anggraini', 'perlu_perbaikan');
    pendaftaranTabApplication('REG-TAB-0003', 'Salsabila Putri Wulandari', 'diterima');
    pendaftaranTabApplication('REG-TAB-0004', 'Rangga Aditya Prakoso', 'ditolak');
    pendaftaranTabApplication('REG-TAB-0005', 'Budi Kurniawan', 'batal');
});

function pendaftaranTabApplication(string $registrationNumber, string $fullName, string $status): void
{
    Application::create([
        'registration_number' => $registrationNumber,
        'full_name' => $fullName,
        'school_name' => 'SMP Negeri 1 Yogyakarta',
        'school_grade' => '9',
        'education_level' => 'SMP',
        'guardian_name' => 'Wali '.$fullName,
        'status' => $status,
        'submitted_at' => now(),
    ]);
}

/**
 * Ambil semua angka badge sebuah label tab dari HTML halaman pendaftaran.
 * Dipakai sekaligus untuk memastikan satu label hanya muncul sekali.
 *
 * @return array<int, string>
 */
function badgeTab(string $html, string $label): array
{
    preg_match_all('/'.preg_quote($label, '/').'\s*<span[^>]*>\s*(\d+)\s*<\/span>/s', $html, $matches);

    return $matches[1];
}

test('tab semua hanya memuat pendaftaran yang masih menunggu aksi', function () {
    $this->get(route('admin.pendaftaran'))
        ->assertOk()
        ->assertSee('Rayhan Pratama')
        ->assertSee('Dewi Anggraini')
        ->assertDontSee('Salsabila Putri Wulandari')
        ->assertDontSee('Rangga Aditya Prakoso')
        ->assertDontSee('Budi Kurniawan');
});

test('tab diterima hanya memuat pendaftaran yang sudah diterima', function () {
    $this->get(route('admin.pendaftaran', ['status' => 'diterima']))
        ->assertOk()
        ->assertSee('Salsabila Putri Wulandari')
        ->assertDontSee('Rayhan Pratama')
        ->assertDontSee('Dewi Anggraini')
        ->assertDontSee('Rangga Aditya Prakoso');
});

test('tab ditolak memuat pendaftaran ditolak sekaligus yang batal', function () {
    $this->get(route('admin.pendaftaran', ['status' => 'ditolak']))
        ->assertOk()
        ->assertSee('Rangga Aditya Prakoso')
        ->assertSee('Budi Kurniawan')
        ->assertDontSee('Rayhan Pratama')
        ->assertDontSee('Salsabila Putri Wulandari');
});

test('tab menunggu review dan perlu perbaikan memisahkan pendaftarannya', function () {
    $this->get(route('admin.pendaftaran', ['status' => 'review']))
        ->assertOk()
        ->assertSee('Rayhan Pratama')
        ->assertDontSee('Dewi Anggraini');

    $this->get(route('admin.pendaftaran', ['status' => 'perbaikan']))
        ->assertOk()
        ->assertSee('Dewi Anggraini')
        ->assertDontSee('Rayhan Pratama');
});

test('status query yang tidak dikenal jatuh ke tab semua', function () {
    $this->get(route('admin.pendaftaran', ['status' => 'ngawur']))
        ->assertOk()
        ->assertSee('Rayhan Pratama')
        ->assertDontSee('Salsabila Putri Wulandari');
});

test('badge tab semua dihitung dari pendaftaran yang masih menunggu', function () {
    $html = $this->get(route('admin.pendaftaran'))->assertOk()->getContent();

    // Dari 5 pendaftaran, hanya 2 yang masih menunggu aksi admin.
    expect(badgeTab($html, 'Semua'))->toBe(['2']);
    expect(badgeTab($html, 'Diterima'))->toBe(['1']);
});

test('tab ditolak tidak lagi dobel dan menggabungkan pendaftaran batal', function () {
    $html = $this->get(route('admin.pendaftaran'))->assertOk()->getContent();

    expect(badgeTab($html, 'Ditolak'))->toBe(['2']);
});
