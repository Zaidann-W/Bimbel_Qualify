<?php

test('kehadiran anak page returns a successful response', function () {
    $response = $this->get('/wali/kehadiran');

    $response->assertStatus(200);
    $response->assertSee('Monitoring Kehadiran');
    $response->assertSee('Kehadiran Fisik');
    $response->assertSee('Riwayat Kehadiran Ananda');
    $response->assertSee('Kehadiran Terakhir');
    $response->assertSee('Hubungi Admin Bimbel');
    $response->assertSee('Notifikasi WhatsApp Otomatis');
});

test('kehadiran anak is reachable from its named route', function () {
    $this->get(route('wali.kehadiran'))->assertStatus(200);
});
