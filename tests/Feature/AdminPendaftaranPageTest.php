<?php

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('pendaftaran returns a successful response', function () {
    $response = $this->get('/admin/pendaftaran');

    $response->assertStatus(200);
    $response->assertSee('Verifikasi &amp; Pengelolaan Pendaftaran Siswa', false);
    $response->assertSee('Pemeriksaan formulir pendaftaran masuk, verifikasi berkas', false);
    $response->assertSee('Menunggu Verifikasi');
    $response->assertSee('Perlu Perbaikan');
    $response->assertSee('Diterima Bulan Ini');
    $response->assertSee('Ditolak / Batal');
});

test('pendaftaran is reachable from its named route', function () {
    $this->get(route('admin.pendaftaran'))->assertStatus(200);
});

test('pendaftaran menu is active on the page', function () {
    $this->get(route('admin.pendaftaran'))
        ->assertStatus(200)
        ->assertSee('Pendaftaran Siswa');
});

test('pendaftaran lists incoming registrations with status and action', function () {
    $response = $this->get(route('admin.pendaftaran'));

    $response->assertStatus(200);
    $response->assertSee('REG-2025-0891');
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Aisyah Nurul Hikmah');
    $response->assertSee('Menunggu Review');
    $response->assertSee('Perbaiki');
});

test('pendaftaran detail opens a registration by its code', function () {
    $response = $this->get(route('admin.pendaftaran.detail', 'REG-2025-0891'));

    $response->assertStatus(200);
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Pendaftaran Siswa');
});

test('guest cannot access pendaftaran page', function () {
    $this->app['auth']->forgetGuards();

    $this->get(route('admin.pendaftaran'))->assertRedirect(route('login'));
});

test('pendaftaran list renders action buttons for every status without error', function () {
    $period = AcademicPeriod::create([
        'academic_year' => '2025/2026',
        'semester' => 'ganjil',
        'starts_on' => now()->subMonths(3),
        'ends_on' => now()->addMonths(3),
        'is_active' => true,
    ]);

    $batch = AdmissionBatch::create([
        'academic_period_id' => $period->id,
        'name' => 'Gelombang 1',
        'wave_number' => 1,
        'opens_at' => now()->subDay(),
        'closes_at' => now()->addDay(),
        'quota' => 100,
    ]);

    $statuses = [
        'menunggu_review' => ['aksi' => 'Review', 'tab' => 'review'],
        'perlu_perbaikan' => ['aksi' => 'Perbaiki', 'tab' => 'perbaikan'],
        'diterima' => ['aksi' => 'Selesai', 'tab' => 'diterima'],
        'ditolak' => ['aksi' => 'Lihat Alasan', 'tab' => 'ditolak'],
    ];

    foreach (array_keys($statuses) as $index => $status) {
        Application::create([
            'registration_number' => 'REG-2026-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            'batch_id' => $batch->id,
            'full_name' => 'Siswa '.$status,
            'nickname' => 'Siswa',
            'birth_date' => '2012-05-12',
            'school_name' => 'SDN Contoh 1',
            'school_grade' => '7',
            'education_level' => 'SMP',
            'phone_wa' => '081234567890',
            'address' => 'Jl. Pendidikan No. 1',
            'guardian_name' => 'Wali '.$status,
            'guardian_phone' => '081234567891',
            'group_wa' => '081234567892',
            'status' => $status,
            'submitted_at' => now(),
        ]);
    }

    // Setiap status kini punya tab sendiri, jadi diuji lewat tab masing-masing.
    foreach ($statuses as $status => $expectasi) {
        $response = $this->get(route('admin.pendaftaran', ['status' => $expectasi['tab']]));

        $response->assertStatus(200);
        $response->assertSee($expectasi['aksi']);
        $response->assertSee('Siswa '.$status);
    }
});
