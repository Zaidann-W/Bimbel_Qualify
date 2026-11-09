<?php

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Ir. Hendra Gunawan',
        'email' => 'hendra.gunawan@email.com',
    ]);

    $this->actingAs($this->user);
    $this->withHeaders(['Accept' => 'application/json']);

    $this->guardian = Guardian::create([
        'user_id' => $this->user->id,
        'name' => 'Ir. Hendra Gunawan',
        'occupation' => 'Wiraswasta',
        'phone_wa' => '+62 812-4455-8891',
        'email' => 'hendra.gunawan@email.com',
        'address' => 'Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan',
    ]);

    $this->student = Student::create([
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
        'phone_wa' => '0812-9843-2210',
        'address' => 'Jl. Tebet Barat Dalam No. 42',
        'school_name' => 'SMAN 8 Jakarta',
        'birth_date' => '2007-05-14',
        'school_grade' => '12',
        'education_level' => 'SMA',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $this->student->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => true,
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
    ]);

    Http::fake([
        'api.xendit.co*' => Http::response([
            'id' => 'inv_sandbox_123',
            'external_id' => 'INV-202611-0001-1-'.$this->user->id,
            'status' => 'PENDING',
            'invoice_url' => 'https://invoices.xendit.co/inv_sandbox_123',
            'expiry_date' => now()->addDays(10)->toISOString(),
        ], 200),
    ]);
});

test('ortu dapat membuat invoice Xendit untuk tagihan milik anaknya', function () {
    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), [
        'invoice_id' => $this->invoice->id,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'invoice_url',
            'xendit_invoice_id',
            'external_id',
            'expired_at',
        ],
    ]);
    $response->assertJson([
        'data' => [
            'invoice_url' => 'https://invoices.xendit.co/inv_sandbox_123',
            'xendit_invoice_id' => 'inv_sandbox_123',
        ],
    ]);

    $this->invoice->refresh();
    expect($this->invoice->xendit_invoice_id)->toBe('inv_sandbox_123');
    expect($this->invoice->xendit_invoice_url)->toBe('https://invoices.xendit.co/inv_sandbox_123');
    expect($this->invoice->external_id)->not()->toBeNull();
    expect($this->invoice->expired_at)->not()->toBeNull();
});

test('mencegah penyerang mengubah amount dari client (anti-tampering)', function () {
    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), [
        'invoice_id' => $this->invoice->id,
        'amount' => 1000, // berusaha menurunkan nominal dari Rp 1.450.000
    ]);

    $response->assertStatus(422);

    // Invoice tidak boleh berubah menjadi terhubung dengan Xendit
    $this->invoice->refresh();
    expect($this->invoice->xendit_invoice_id)->toBeNull();
});

test('ortu tidak dapat membuat invoice untuk tagihan anak orang lain', function () {
    $userLain = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Budi Santoso',
        'email' => 'budi@email.com',
    ]);

    $guardianLain = Guardian::create([
        'user_id' => $userLain->id,
        'name' => 'Budi Santoso',
        'phone_wa' => '+62 812-0000-0000',
        'email' => 'budi@email.com',
        'address' => 'Jl. Sudirman No. 1',
    ]);

    $anakLain = Student::create([
        'full_name' => 'Citra Lestari',
        'nisn' => '0098123499',
        'school_name' => 'SMA 1 Jakarta',
        'school_grade' => '11',
        'education_level' => 'SMA',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $anakLain->id,
        'guardian_id' => $guardianLain->id,
        'relationship' => 'ayah',
        'is_financial_responsible' => true,
    ]);

    $invoiceLain = Invoice::create([
        'student_id' => $anakLain->id,
        'invoice_number' => 'INV-202611-0099',
        'type' => 'spp_bulanan',
        'description' => 'SPP November 2026 - Kelas 11 IPA',
        'amount' => 2000000,
        'due_date' => now()->addDays(10),
        'status' => 'belum_bayar',
    ]);

    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), [
        'invoice_id' => $invoiceLain->id,
    ]);

    $response->assertStatus(403);
});

test('tidak dapat membuat invoice untuk tagihan yang sudah lunas', function () {
    $this->invoice->update(['status' => 'lunas']);

    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), [
        'invoice_id' => $this->invoice->id,
    ]);

    $response->assertStatus(422);

    $this->invoice->refresh();
    expect($this->invoice->xendit_invoice_id)->toBeNull();
});

test('invoice yang tidak ditemukan mengembalikan 404', function () {
    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), [
        'invoice_id' => 999999,
    ]);

    $response->assertStatus(404);
});

test('invoice_id wajib diisi', function () {
    $response = $this->post(route('ortu.pembayaran.xendit.invoice'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['invoice_id']);
});
