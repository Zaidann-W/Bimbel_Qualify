<?php

test('qualifix returns a successful response', function () {
    $response = $this->get('/admin/qualifix');

    $response->assertStatus(200);
    $response->assertSee('QualiFix');
    $response->assertSee('Daftar pertanyaan dan sesi konsultasi tugas sekolah siswa.');
    $response->assertSee('Total Masuk');
    $response->assertSee('Menunggu Tentor');
    $response->assertSee('Perlu Respon');
    $response->assertSee('Terselesaikan');
    $response->assertSee('Ketersediaan Tentor');
    $response->assertSee('Tentor Pendamping Klinik PR');
});

test('qualifix is reachable from its named route', function () {
    $this->get(route('admin.qualifix'))->assertStatus(200);
});

test('qualifix menu is active on the page', function () {
    $this->get(route('admin.qualifix'))
        ->assertStatus(200)
        ->assertSee('QualiFix');
});

test('qualifix lists consultation tickets with status and action', function () {
    $response = $this->get(route('admin.qualifix'));

    $response->assertStatus(200);
    $response->assertSee('#KPR-0824');
    $response->assertSee('Integral Substitusi Trigonometri');
    $response->assertSee('Arya Danendra');
    $response->assertSee('Tugaskan Tentor');
    $response->assertSee('Tata Nama Senyawa Benzena');
    $response->assertSee('Tentor: Kak Gilang');
    $response->assertSee('Lihat Detail');
    $response->assertSee('Hortatory Exposition Text');
    $response->assertSee('Lihat Solusi');
    $response->assertSee('Tiket Konsultasi');
});

test('qualifix shows standby tutors and ticket counters', function () {
    $response = $this->get(route('admin.qualifix'));

    $response->assertStatus(200);
    $response->assertSee('2 Standby');
    $response->assertSee('Kak Gilang');
    $response->assertSee('Kak Sarah');
    $response->assertSee('Kak Dimas');
    $response->assertSee('Membimbing');
    $response->assertSee('Tersedia');
    $response->assertSee('Semua');
    $response->assertSee('Selesai');
});
