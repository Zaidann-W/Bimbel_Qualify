<?php

test('absensi dan jadwal returns a successful response', function () {
    $response = $this->get('/admin/absensi-jadwal');

    $response->assertStatus(200);
    $response->assertSee('Manajemen Jadwal &amp; Presensi Bimbel', false);
    $response->assertSee('Pemantauan jadwal sesi kelas, presensi kehadiran tentor dan siswa, serta pencatatan izin / sakit / alpa secara terpadu.', false);
    $response->assertSee('Total Sesi Hari Ini');
    $response->assertSee('Kehadiran Tentor');
    $response->assertSee('Daftar Kelas — Sesi Sore (15:30 - 17:30)');
    $response->assertSee('R. Bima (Lt. 2)');
    $response->assertSee('Verifikasi Izin &amp; Sakit', false);
    $response->assertSee('Ketersediaan Ruangan');
    $response->assertSee('Kepadatan Jadwal Pekan Ini (Oktober 2024)');
});

test('absensi dan jadwal is reachable from its named route', function () {
    $this->get(route('admin.absensiJadwal'))->assertStatus(200);
});

test('absensi dan jadwal menu is active on the page', function () {
    $this->get(route('admin.absensiJadwal'))
        ->assertStatus(200)
        ->assertSee('Absensi &amp; Jadwal', false);
});

test('absensi dan jadwal lists both learning sessions', function () {
    $response = $this->get(route('admin.absensiJadwal'));

    $response->assertStatus(200);
    $response->assertSee('Sesi Sore');
    $response->assertSee('Sesi Malam');
    $response->assertSee('Check-in 15:15');
    $response->assertSee('Check-in belum dibuka');
    $response->assertSee('3 Menunggu');
});
