<?php

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

function insertAksiCepatApplication(string $registrationNumber, string $fullName, string $status, string $submittedAt): void
{
    DB::table('applications')->insert([
        'registration_number' => $registrationNumber,
        'full_name' => $fullName,
        'school_name' => 'SMP Negeri 1 Yogyakarta',
        'school_grade' => '9',
        'status' => $status,
        'submitted_at' => $submittedAt,
        'created_at' => $submittedAt,
        'updated_at' => $submittedAt,
    ]);
}

/**
 * @return array<int, array<string, mixed>>
 */
function aksiCepatRegistrations(): array
{
    return app(DashboardService::class)->adminDashboard()['registrations'];
}

test('pendaftar yang sudah diterima tidak tampil di card aksi cepat', function () {
    insertAksiCepatApplication('REG-2026-0001', 'Salsabila Putri Wulandari', 'diterima', '2026-11-10 09:00:00');

    expect(aksiCepatRegistrations())->toBeEmpty();

    $this->get('/admin')
        ->assertOk()
        ->assertDontSee('Salsabila Putri Wulandari');
});

test('pendaftar yang ditolak tidak tampil di card aksi cepat', function () {
    insertAksiCepatApplication('REG-2026-0002', 'Rangga Aditya Prakoso', 'ditolak', '2026-11-10 10:00:00');

    expect(aksiCepatRegistrations())->toBeEmpty();

    $this->get('/admin')
        ->assertOk()
        ->assertDontSee('Rangga Aditya Prakoso');
});

test('pendaftar menunggu review dan perlu perbaikan tetap tampil di card aksi cepat', function () {
    insertAksiCepatApplication('REG-2026-0003', 'Dewi Anggraini', 'menunggu_review', '2026-11-11 08:00:00');
    insertAksiCepatApplication('REG-2026-0004', 'Fajar Nugroho', 'perlu_perbaikan', '2026-11-11 09:00:00');

    $registrations = aksiCepatRegistrations();

    expect($registrations)->toHaveCount(2);
    expect(collect($registrations)->pluck('name')->all())->toBe(['Fajar Nugroho', 'Dewi Anggraini']);

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Dewi Anggraini')
        ->assertSee('Fajar Nugroho');
});

test('total pendaftaran hanya menghitung status yang masih menunggu aksi', function () {
    insertAksiCepatApplication('REG-2026-0005', 'Salsabila Putri Wulandari', 'diterima', '2026-11-10 09:00:00');
    insertAksiCepatApplication('REG-2026-0006', 'Rangga Aditya Prakoso', 'ditolak', '2026-11-10 10:00:00');
    insertAksiCepatApplication('REG-2026-0007', 'Dewi Anggraini', 'menunggu_review', '2026-11-11 08:00:00');
    insertAksiCepatApplication('REG-2026-0008', 'Fajar Nugroho', 'perlu_perbaikan', '2026-11-11 09:00:00');

    expect(app(DashboardService::class)->adminDashboard()['registrationTotal'])->toBe(2);
});
