<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

/**
 * Rute ekspor operasional beserta kolom khas CSV-nya.
 *
 * @return array<string, array{rute: string, kolom: string}>
 */
function ruteEksporOperasional(): array
{
    return [
        'keuangan' => ['rute' => 'admin.laporanKeuangan.export', 'kolom' => 'No Invoice'],
        'presensi' => ['rute' => 'admin.laporanPresensi.export', 'kolom' => 'Status Kehadiran'],
        'pendaftaran' => ['rute' => 'admin.laporanPendaftaran.export', 'kolom' => 'Dicatat Oleh'],
        'qualifix' => ['rute' => 'admin.laporanQualifix.export', 'kolom' => 'Mata Pelajaran'],
    ];
}

foreach (ruteEksporOperasional() as $nama => $konfig) {
    test("ekspor {$nama} csv memuat baris judul kolom", function () use ($konfig) {
        $response = $this->get(route($konfig['rute'], ['format' => 'csv']));

        $response->assertStatus(200);
        expect($response->headers->get('content-disposition'))->toContain('.csv');

        $isi = $response->streamedContent();
        expect($isi)->toContain($konfig['kolom']);
    });

    test("ekspor {$nama} pdf menghasilkan berkas pdf yang valid", function () use ($konfig) {
        $response = $this->get(route($konfig['rute'], ['format' => 'pdf']));

        $response->assertStatus(200);
        expect($response->headers->get('content-disposition'))->toContain('.pdf');

        $isi = $response->streamedContent();
        expect($isi)->toStartWith('%PDF');
    });
}

test('ekspor memakai format bawaan csv bila parameter format tidak diisi', function () {
    $response = $this->get(route('admin.laporanKeuangan.export'));

    $response->assertStatus(200);
    expect($response->headers->get('content-disposition'))->toContain('.csv');
});
