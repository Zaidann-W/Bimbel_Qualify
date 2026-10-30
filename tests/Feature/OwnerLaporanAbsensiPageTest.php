<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'owner', 'status' => 'aktif']));
});

test('laporan absensi owner page returns a successful response', function () {
    $response = $this->get('/owner/laporan-absensi');

    $response->assertStatus(200);
    $response->assertSee('Laporan Kehadiran & Rekap Absensi', false);
    $response->assertSee('Kehadiran Siswa');
    $response->assertSee('Ketepatan Waktu Tentor');
    $response->assertSee('Total Sesi Terlaksana');
    $response->assertSee('Rekap Kehadiran Siswa per Jenjang');
    $response->assertSee('Rekap Mengajar Tentor');
});

test('laporan absensi owner page accepts periode and jenjang filters', function () {
    $response = $this->get(route('owner.laporanAbsensi', [
        'periode' => 'TA',
        'jenjang' => 'SMA',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Laporan Kehadiran & Rekap Absensi', false);
});

test('laporan absensi owner can export csv for kelas and tentor', function (string $tab) {
    $response = $this->get(route('owner.laporanAbsensi', ['export' => 'csv', 'tab' => $tab]));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
})->with(['jenjang', 'tentor']);
