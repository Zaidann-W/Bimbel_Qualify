<?php

use App\Models\Application;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Ir. Hendra Gunawan',
        'email' => 'hendra.gunawan@email.com',
    ]);

    $this->guardian = Guardian::create([
        'user_id' => $this->user->id,
        'name' => 'Ir. Hendra Gunawan',
        'phone_wa' => '+62 812-4455-8891',
        'email' => 'hendra.gunawan@email.com',
        'address' => 'Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan',
    ]);

    $this->student = Student::create([
        'full_name' => 'Aisyah Putri Azzahra',
        'nisn' => '0098123471',
        'school_name' => 'SMAN 8 Jakarta',
        'school_grade' => '12',
        'education_level' => 'SMA',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $this->student->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_financial_responsible' => true,
    ]);

    $this->invoice = Invoice::create([
        'student_id' => $this->student->id,
        'invoice_number' => 'INV-202611-0001',
        'type' => 'spp_bulanan',
        'description' => 'SPP November 2026 - Kelas 12 IPA',
        'amount' => 1450000,
        'due_date' => now()->addDays(10),
        'status' => 'belum_bayar',
        'external_id' => 'INV-202611-0001-5',
        'xendit_invoice_id' => 'inv_sandbox_123',
        'xendit_invoice_url' => 'https://invoices.xendit.co/inv_sandbox_123',
    ]);
});

test('webhook dengan token yang valid dan status PAID memperbarui invoice menjadi lunas', function () {
    $payload = [
        'id' => 'inv_sandbox_123',
        'external_id' => 'INV-202611-0001-5',
        'status' => 'PAID',
        'paid_amount' => 1450000,
        'payment_method' => 'QRIS',
        'transaction_id' => 'trx_xendit_001',
    ];

    $response = $this->post('/webhook/xendit', $payload, [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('lunas');
    expect($this->invoice->paid_at)->not()->toBeNull();

    $this->assertDatabaseHas('payments', [
        'invoice_id' => $this->invoice->id,
        'xendit_payment_id' => 'inv_sandbox_123',
        'xendit_external_id' => 'INV-202611-0001-5',
        'xendit_status' => 'PAID',
        'status' => 'berhasil',
    ]);
});

test('webhook dengan token yang salah mengembalikan 401', function () {
    $response = $this->post('/webhook/xendit', [
        'external_id' => 'INV-202611-0001-5',
        'status' => 'PAID',
    ], [
        'X-Callback-Token' => 'salah-token',
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(401);
});

test('webhook idempotent: memproses external_id yang sama dua kali tidak membuat duplikasi pembayaran', function () {
    $payload = [
        'id' => 'trx_xendit_002',
        'external_id' => 'INV-202611-0001-5',
        'status' => 'PAID',
        'paid_amount' => 1450000,
        'payment_method' => 'VA_BCA',
    ];

    // Kirim dua kali
    $this->post('/webhook/xendit', $payload, [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ]);
    $response = $this->post('/webhook/xendit', $payload, [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);

    expect(Payment::where('xendit_external_id', 'INV-202611-0001-5')->count())->toBe(1);
});

test('webhook dengan status EXPIRED menandai invoice sebagai batal', function () {
    $payload = [
        'id' => 'trx_xendit_003',
        'external_id' => 'INV-202611-0001-5',
        'status' => 'EXPIRED',
        'paid_amount' => 0,
        'payment_method' => 'QRIS',
    ];

    $response = $this->post('/webhook/xendit', $payload, [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('batal');
});

test('webhook invoice pendaftaran menandai pembayaran pendaftaran menjadi lunas', function () {
    $application = Application::create([
        'registration_number' => 'REG-2026-0001',
        'full_name' => 'Budi Santoso',
        'phone_wa' => '081234567890',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
    ]);

    $response = $this->post('/webhook/xendit', [
        'id' => 'inv_pendaftaran_001',
        'external_id' => 'REG-'.$application->registration_number.'-a1b2c3d4',
        'status' => 'PAID',
        'paid_amount' => 500000,
    ], [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $application->refresh();
    expect($application->payment_status)->toBe('lunas')
        ->and($application->payment_verified_at)->not->toBeNull();
});

test('webhook invoice pendaftaran dicocokkan lewat external id yang tersimpan', function () {
    $application = Application::create([
        'registration_number' => 'REG-2026-0003',
        'full_name' => 'Budi Santoso',
        'phone_wa' => '081234567890',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
        'xendit_invoice_id' => 'inv_pendaftaran_003',
        'xendit_external_id' => 'REG-REG-2026-0003-a1b2c3d4',
    ]);

    $this->post('/webhook/xendit', [
        'id' => 'inv_pendaftaran_003',
        'external_id' => 'REG-REG-2026-0003-a1b2c3d4',
        'status' => 'PAID',
        'paid_amount' => 500000,
    ], [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ])->assertStatus(200)->assertJson(['success' => true]);

    $application->refresh();
    expect($application->payment_status)->toBe('lunas')
        ->and($application->payment_verified_at)->not->toBeNull();
});

test('endpoint webhook tidak memerlukan token CSRF', function () {
    $application = Application::create([
        'registration_number' => 'REG-2026-0004',
        'full_name' => 'Siti Aminah',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
    ]);

    // Tanpa token CSRF: kalau route masih dilindungi CSRF, jawabannya 419.
    $this->post('/webhook/xendit', [
        'external_id' => 'REG-REG-2026-0004-a1b2c3d4',
        'status' => 'PAID',
    ], [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ])->assertStatus(200)->assertJson(['success' => true]);

    expect($application->refresh()->payment_status)->toBe('lunas');
});

test('webhook dengan token yang salah tetap ditolak', function () {
    $this->post('/webhook/xendit', [
        'external_id' => 'REG-REG-2026-0005-a1b2c3d4',
        'status' => 'PAID',
    ], [
        'X-Callback-Token' => 'token-salah',
        'Accept' => 'application/json',
    ])->assertStatus(401);
});

test('webhook invoice pendaftaran dengan status selain PAID tidak menandai lunas', function () {
    $application = Application::create([
        'registration_number' => 'REG-2026-0002',
        'full_name' => 'Siti Aminah',
        'phone_wa' => '081234567890',
        'status' => 'diterima',
        'payment_status' => 'belum_bayar',
    ]);

    $this->post('/webhook/xendit', [
        'id' => 'inv_pendaftaran_002',
        'external_id' => 'REG-'.$application->registration_number.'-b2c3d4e5',
        'status' => 'EXPIRED',
    ], [
        'X-Callback-Token' => config('xendit.callback_token'),
        'Accept' => 'application/json',
    ])->assertStatus(200);

    expect($application->refresh()->payment_status)->toBe('belum_bayar');
});
