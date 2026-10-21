<?php

test('qualifix siswa page returns a successful response', function () {
    $response = $this->get('/siswa/qualifix');

    $response->assertStatus(200);
    $response->assertSee('Total Diajukan');
    $response->assertSee('Menunggu Jawaban');
    $response->assertSee('Barisan Geometri Bertingkat');
    $response->assertSee('Panduan Tanya Soal');
    $response->assertSee('Ajukan Pertanyaan PR Baru');
    $response->assertSee('Kirim Pertanyaan');
});

test('qualifix siswa is reachable from its named route', function () {
    $this->get(route('siswa.qualifix'))->assertStatus(200);
});
