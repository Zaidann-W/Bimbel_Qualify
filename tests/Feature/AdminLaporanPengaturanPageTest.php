<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('laporan dan pengaturan returns a successful response', function () {
    $response = $this->get('/admin/laporan-pengaturan');

    $response->assertStatus(200);
    $response->assertSee('Laporan &amp; Pengaturan', false);
    $response->assertSee('Unduh berkas rekapitulasi operasional dan pengaturan preferensi sistem.', false);
    $response->assertSee('Simpan Pengaturan');
    $response->assertSee('Ekspor Laporan Operasional');
    $response->assertSee('Pilih berkas rekapitulasi berkala dalam format Excel atau PDF', false);
});

test('laporan dan pengaturan is reachable from its named route', function () {
    $this->get(route('admin.laporanPengaturan'))->assertStatus(200);
});

test('laporan dan pengaturan menu is active on the page', function () {
    $this->get(route('admin.laporanPengaturan'))
        ->assertStatus(200)
        ->assertSee('Laporan &amp; Pengaturan', false);
});

test('laporan dan pengaturan lists every exportable report in both formats', function () {
    $response = $this->get(route('admin.laporanPengaturan'));

    $response->assertStatus(200);
    $response->assertSee('Laporan Keuangan &amp; SPP', false);
    $response->assertSee('Arus kas, uang gedung, dan pelunasan SPP', false);
    $response->assertSee('Presensi &amp; Jadwal Mengajar', false);
    $response->assertSee('Kehadiran siswa dan jam mengajar tentor', false);
    $response->assertSee('Pendaftaran Siswa Baru');
    $response->assertSee('Data pendaftar baru dan status registrasi berkas', false);
    $response->assertSee('Rekap Sesi Klinik PR');
    $response->assertSee('Sesi konsultasi belajar dan rekap materi', false);
    $response->assertSee('Excel');
    $response->assertSee('PDF');
});

test('laporan dan pengaturan shows whatsapp notification switches', function () {
    $response = $this->get(route('admin.laporanPengaturan'));

    $response->assertStatus(200);
    $response->assertSee('Notifikasi WhatsApp');
    $response->assertSee('Kirim otomatis ke nomor orang tua / siswa', false);
    $response->assertSee('Kirim bukti struk pembayaran otomatis', false);
    $response->assertSee('Pemberitahuan ketidakhadiran kelas', false);
    $response->assertSee('Pengingat tagihan SPP jatuh tempo', false);
});

test('laporan dan pengaturan shows academic period and branch settings', function () {
    $response = $this->get(route('admin.laporanPengaturan'));

    $response->assertStatus(200);
    $response->assertSee('Periode &amp; Cabang Bimbel', false);
    $response->assertSee('Parameter operasional dan kalender akademik', false);
    $response->assertSee('Tahun Ajaran');
    $response->assertSee('2024/2025');
    $response->assertSee('Semester');
    $response->assertSee('Semester Ganjil');
    $response->assertSee('Penanggung Jawab Cabang', false);
    $response->assertSee('Ust. Farhan Kamil, M.Pd.');
    $response->assertSee('Branch Director');
});
