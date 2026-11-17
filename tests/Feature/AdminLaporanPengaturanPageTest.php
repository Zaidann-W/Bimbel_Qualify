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
    $response->assertSee('Rekap Sesi QualiFix');
    $response->assertSee('Sesi konsultasi belajar dan rekap materi', false);
    $response->assertSee('Excel');
    $response->assertSee('PDF');
});

test('laporan dan pengaturan links to every operational export route', function () {
    $response = $this->get(route('admin.laporanPengaturan'));

    $response->assertStatus(200);

    foreach ([
        'admin.laporanKeuangan.export',
        'admin.laporanPresensi.export',
        'admin.laporanPendaftaran.export',
        'admin.laporanQualifix.export',
    ] as $routeName) {
        $response->assertSee(route($routeName), false);
    }
});

test('laporan dan pengaturan menyimpan preferensi sistem', function () {
    $this->post(route('admin.pengaturan.update'), [
        'tahun_ajaran' => '2025/2026',
        'semester' => 'Semester Genap',
        'penanggung_jawab' => 'Ust. Uji Coba, M.Pd.',
        'biaya_pendaftaran' => 150000,
        'notif_struk' => '1',
        'notif_absensi' => '0',
        'notif_tagihan' => '1',
    ])->assertRedirect();

    $this->assertDatabaseHas('website_settings', ['key' => 'tahun_ajaran', 'value' => '2025/2026']);
    $this->assertDatabaseHas('website_settings', ['key' => 'semester', 'value' => 'Semester Genap']);
    $this->assertDatabaseHas('website_settings', ['key' => 'penanggung_jawab', 'value' => 'Ust. Uji Coba, M.Pd.']);
    $this->assertDatabaseHas('website_settings', ['key' => 'biaya_pendaftaran', 'value' => 150000]);
    $this->assertDatabaseHas('website_settings', ['key' => 'notif_struk', 'value' => '1']);
    $this->assertDatabaseHas('website_settings', ['key' => 'notif_absensi', 'value' => '0']);
    $this->assertDatabaseHas('website_settings', ['key' => 'notif_tagihan', 'value' => '1']);
});

test('laporan dan pengaturan menampilkan nilai tersimpan di form', function () {
    $this->post(route('admin.pengaturan.update'), [
        'tahun_ajaran' => '2030/2031',
        'semester' => 'Semester Genap',
        'penanggung_jawab' => 'Ust. Tersimpan, M.Pd.',
        'biaya_pendaftaran' => 250000,
        'notif_struk' => '1',
        'notif_absensi' => '1',
        'notif_tagihan' => '1',
    ]);

    $this->get(route('admin.laporanPengaturan'))
        ->assertStatus(200)
        ->assertSee('2030/2031')
        ->assertSee('Ust. Tersimpan, M.Pd.')
        ->assertSee('250000');
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
