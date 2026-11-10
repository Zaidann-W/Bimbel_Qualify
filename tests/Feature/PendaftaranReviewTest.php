<?php

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\StudyShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);
    $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'aktif']);
    $this->tentor = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);
    $this->siswa = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);
    $this->ortu = User::factory()->create(['role' => 'ortu', 'status' => 'aktif']);

    $this->period = AcademicPeriod::create([
        'academic_year' => '2025/2026',
        'semester' => 'ganjil',
        'starts_on' => now()->subMonths(3),
        'ends_on' => now()->addMonths(3),
        'is_active' => true,
    ]);

    $this->batch = AdmissionBatch::create([
        'academic_period_id' => $this->period->id,
        'name' => 'Gelombang 1',
        'wave_number' => 1,
        'opens_at' => now()->subDay(),
        'closes_at' => now()->addDay(),
        'quota' => 100,
    ]);

    $this->shift = StudyShift::create([
        'name' => 'Sesi Sore',
        'starts_at' => '15:30:00',
        'ends_at' => '17:30:00',
    ]);
});

function pendingApplication(array $overrides = []): Application
{
    return Application::create(array_merge([
        'registration_number' => 'REG-2026-0001',
        'batch_id' => app(AdmissionBatch::class)::first()?->id ?? AdmissionBatch::factory()?->create()?->id,
        'full_name' => 'Budi Santoso',
        'nickname' => 'Budi',
        'birth_date' => '2012-05-12',
        'school_name' => 'SDN Contoh 1',
        'school_grade' => '7',
        'education_level' => 'SMP',
        'phone_wa' => '081234567890',
        'address' => 'Jl. Pendidikan No. 1',
        'guardian_name' => 'Siti Aminah',
        'guardian_phone' => '081234567891',
        'group_wa' => '081234567892',
        'status' => 'menunggu_review',
        'submitted_at' => now(),
    ], $overrides));
}

/* ============================================================
   Admin update status
   ============================================================ */

test('admin can approve application status to diterima', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'diterima',
            'review_note' => 'Lolos verifikasi berkas.',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->status)->toBe('diterima')
        ->and($app->reviewed_by)->toBe($this->admin->id)
        ->and($app->reviewed_at)->not->toBeNull()
        ->and($app->status_reason)->toBe('Lolos verifikasi berkas.');

    expect(ApplicationStatusHistory::where('application_id', $app->id)
        ->where('old_status', 'menunggu_review')
        ->where('new_status', 'diterima')
        ->where('changed_by', $this->admin->id)
        ->exists())->toBeTrue();
});

test('admin can reject application with review note', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'ditolak',
            'review_note' => 'Dokumen tidak lengkap.',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->status)->toBe('ditolak')
        ->and($app->status_reason)->toBe('Dokumen tidak lengkap.');

    expect(ApplicationStatusHistory::where('application_id', $app->id)
        ->where('old_status', 'menunggu_review')
        ->where('new_status', 'ditolak')
        ->exists())->toBeTrue();
});

test('admin can request revision with review note', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'perlu_perbaikan',
            'review_note' => 'Upload ulang rapor semester genap.',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->status)->toBe('perlu_perbaikan')
        ->and($app->status_reason)->toBe('Upload ulang rapor semester genap.');
});

test('review note is required when rejecting application', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'ditolak',
        ])
        ->assertSessionHasErrors('review_note');

    $app->refresh();
    expect($app->status)->toBe('menunggu_review');
});

test('review note is required when requesting revision', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'perlu_perbaikan',
        ])
        ->assertSessionHasErrors('review_note');

    $app->refresh();
    expect($app->status)->toBe('menunggu_review');
});

test('review note is optional when approving application', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'diterima',
        ])
        ->assertRedirect(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->status)->toBe('diterima');
});

test('invalid status is rejected', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'invalid_status',
        ])
        ->assertSessionHasErrors('status');

    $app->refresh();
    expect($app->status)->toBe('menunggu_review');
});

test('cannot change status from diterima to menunggu_review (invalid transition)', function () {
    $app = pendingApplication(['status' => 'diterima']);

    $this->actingAs($this->admin)
        ->patch(route('admin.pendaftaran.status', $app->registration_number), [
            'status' => 'menunggu_review',
            'review_note' => 'Kembali ke menunggu.',
        ])
        ->assertSessionHasErrors('status');

    $app->refresh();
    expect($app->status)->toBe('diterima');
});

/* ============================================================
   Authorization
   ============================================================ */

test('non-admin roles cannot access status update', function () {
    $app = pendingApplication();

    foreach ([
        $this->manager,
        $this->tentor,
        $this->siswa,
        $this->ortu,
    ] as $user) {
        $this->actingAs($user)
            ->patch(route('admin.pendaftaran.status', $app->registration_number), [
                'status' => 'diterima',
                'review_note' => 'Test.',
            ])
            ->assertStatus(302)
            ->assertRedirect(route($user->role.'.dashboard'));
    }
});

test('guest cannot access status update and is redirected to login', function () {
    $app = pendingApplication();

    $this->patch(route('admin.pendaftaran.status', $app->registration_number), [
        'status' => 'diterima',
        'review_note' => 'Test.',
    ])->assertRedirect(route('login'));
});

test('admin can view pendaftaran detail page', function () {
    $app = pendingApplication();

    $this->actingAs($this->admin)
        ->get(route('admin.pendaftaran.detail', $app->registration_number))
        ->assertStatus(200)
        ->assertSee($app->full_name)
        ->assertSee($app->registration_number);
});

/* ============================================================
   Payment page - only accessible for approved applications
   ============================================================ */

test('payment page is accessible for approved application', function () {
    $app = pendingApplication(['status' => 'diterima']);

    $this->get(route('pendaftaran.bayar', $app->registration_number))
        ->assertStatus(200)
        ->assertSee($app->registration_number);
});

test('payment page rejects application that is not yet approved', function () {
    $app = pendingApplication(['status' => 'menunggu_review']);

    $this->get(route('pendaftaran.bayar', $app->registration_number))
        ->assertStatus(403);
});

test('payment page rejects application that is already lunas', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
    ]);

    $this->get(route('pendaftaran.bayar', $app->registration_number))
        ->assertStatus(403);
});

/* ============================================================
   Payment submission
   ============================================================ */

test('payment submission saves receipt file and sets menunggu_verifikasi', function () {
    $app = pendingApplication(['status' => 'diterima']);

    $this->post(route('pendaftaran.bayar.simpan', $app->registration_number), [
        'payment_method' => 'transfer_bank',
        'bukti_transfer' => UploadedFile::fake()->image('bukti.jpg'),
    ])->assertRedirect(route('pendaftaran.bayar', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->payment_status)->toBe('menunggu_verifikasi')
        ->and($app->payment_proof_path)->not->toBeNull();
});

test('payment submission without file still works for xendit method', function () {
    $app = pendingApplication(['status' => 'diterima']);

    $this->post(route('pendaftaran.bayar.simpan', $app->registration_number), [
        'payment_method' => 'xendit',
    ])->assertRedirect(route('pendaftaran.bayar', $app->registration_number))
        ->assertSessionHas('success');

    $app->refresh();
    expect($app->payment_status)->toBe('menunggu_verifikasi');
});

/* ============================================================
   Account creation after payment
   ============================================================ */

test('account creation page is accessible only after payment lunas', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
    ]);

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertStatus(200)
        ->assertSee($app->registration_number);
});

test('account creation page rejects if payment not yet lunas', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'menunggu_verifikasi',
    ]);

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertStatus(403);
});

test('account creation creates ortu and siswa users with correct roles', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
    ]);

    $this->post(route('pendaftaran.buatAkun.simpan', $app->registration_number), [
        'ortu_name' => 'Siti Aminah',
        'ortu_email' => 'ortu@example.com',
        'ortu_phone' => '081234567891',
        'ortu_password' => 'rahasia123',
        'ortu_password_confirmation' => 'rahasia123',
        'siswa_name' => 'Budi Santoso',
        'siswa_email' => 'siswa@example.com',
        'siswa_password' => 'rahasia123',
        'siswa_password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('pendaftaran.berhasil', $app->registration_number))
        ->assertSessionHas('success');

    $ortu = User::where('email', 'ortu@example.com')->first();
    $siswa = User::where('email', 'siswa@example.com')->first();

    expect($ortu)->not->toBeNull()
        ->and($ortu->role)->toBe('ortu')
        ->and($ortu->name)->toBe('Siti Aminah');

    expect($siswa)->not->toBeNull()
        ->and($siswa->role)->toBe('siswa')
        ->and($siswa->name)->toBe('Budi Santoso');

    $app->refresh();
    expect($app->accounts_created)->toBeTrue();
});

test('email must be unique for ortu and siswa', function () {
    User::factory()->create(['email' => 'ortu@example.com', 'role' => 'ortu']);

    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
    ]);

    $this->post(route('pendaftaran.buatAkun.simpan', $app->registration_number), [
        'ortu_name' => 'Siti Aminah',
        'ortu_email' => 'ortu@example.com',
        'ortu_phone' => '081234567891',
        'ortu_password' => 'rahasia123',
        'ortu_password_confirmation' => 'rahasia123',
        'siswa_name' => 'Budi Santoso',
        'siswa_email' => 'siswa@example.com',
        'siswa_password' => 'rahasia123',
        'siswa_password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('ortu_email');
});

test('password confirmation must match', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
    ]);

    $this->post(route('pendaftaran.buatAkun.simpan', $app->registration_number), [
        'ortu_name' => 'Siti Aminah',
        'ortu_email' => 'ortu@example.com',
        'ortu_phone' => '081234567891',
        'ortu_password' => 'rahasia123',
        'ortu_password_confirmation' => 'wrong',
        'siswa_name' => 'Budi Santoso',
        'siswa_email' => 'siswa@example.com',
        'siswa_password' => 'rahasia123',
        'siswa_password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('ortu_password_confirmation');
});

test('cannot create account twice (accounts_created already true)', function () {
    $app = pendingApplication([
        'status' => 'diterima',
        'payment_status' => 'lunas',
        'accounts_created' => true,
    ]);

    $this->get(route('pendaftaran.buatAkun', $app->registration_number))
        ->assertStatus(403);
});
