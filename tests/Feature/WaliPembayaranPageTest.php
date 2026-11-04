<?php

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

    $this->actingAs($this->user);

    $this->guardian = Guardian::create([
        'user_id' => $this->user->id,
        'name' => 'Ir. Hendra Gunawan',
        'occupation' => 'Wiraswasta',
        'phone_wa' => '+62 812-4455-8891',
        'email' => 'hendra.gunawan@email.com',
        'address' => 'Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan',
    ]);

    $this->anak1 = Student::create([
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

    $this->anak2 = Student::create([
        'full_name' => 'Anindya Putri Rahayu',
        'nickname' => 'Anindya',
        'nisn' => '0098123472',
        'phone_wa' => '0812-9843-2211',
        'address' => 'Jl. Menteng Raya No. 10',
        'school_name' => 'SMA 71 Jakarta',
        'birth_date' => '2009-03-20',
        'school_grade' => '10',
        'education_level' => 'SMA',
        'major' => 'IPS',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $this->anak1->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
    ]);

    StudentGuardian::create([
        'student_id' => $this->anak2->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => false,
        'is_financial_responsible' => false,
    ]);

    // Tagihan anak 1: SPP April 2025 belum lunas (Rp 1.450.000)
    $this->invoiceAnak1April = Invoice::create([
        'student_id' => $this->anak1->id,
        'invoice_number' => 'INV-202504-0001',
        'type' => 'spp_bulanan',
        'description' => 'SPP April 2025 - Kelas 12 IPA',
        'amount' => 1450000,
        'due_date' => '2025-04-10',
        'status' => 'belum_bayar',
    ]);

    // Tagihan anak 1: SPP Maret 2025 sudah lunas (Rp 1.450.000)
    $this->invoiceAnak1Maret = Invoice::create([
        'student_id' => $this->anak1->id,
        'invoice_number' => 'INV-202503-0002',
        'type' => 'spp_bulanan',
        'description' => 'SPP Maret 2025 - Kelas 12 IPA',
        'amount' => 1450000,
        'due_date' => '2025-03-10',
        'status' => 'lunas',
    ]);
    Payment::create([
        'invoice_id' => $this->invoiceAnak1Maret->id,
        'payment_method' => 'qris',
        'amount' => 1450000,
        'transaction_ref' => 'QRIS-202503-0001',
        'status' => 'berhasil',
        'paid_at' => now()->subDays(10),
    ]);

    // Tagihan anak 1: SPP Februari 2025 sudah lunas (Rp 1.450.000)
    $this->invoiceAnak1Februari = Invoice::create([
        'student_id' => $this->anak1->id,
        'invoice_number' => 'INV-202502-0003',
        'type' => 'spp_bulanan',
        'description' => 'SPP Februari 2025 - Kelas 12 IPA',
        'amount' => 1450000,
        'due_date' => '2025-02-10',
        'status' => 'lunas',
    ]);
    Payment::create([
        'invoice_id' => $this->invoiceAnak1Februari->id,
        'payment_method' => 'transfer_bank',
        'amount' => 1450000,
        'transaction_ref' => 'TRF-202502-0001',
        'status' => 'berhasil',
        'paid_at' => now()->subDays(40),
    ]);

    // Tagihan anak 2: SPP April 2025 belum lunas (Rp 1.450.000)
    $this->invoiceAnak2April = Invoice::create([
        'student_id' => $this->anak2->id,
        'invoice_number' => 'INV-202504-0004',
        'type' => 'spp_bulanan',
        'description' => 'SPP April 2025 - Kelas 10 IPS',
        'amount' => 1450000,
        'due_date' => '2025-04-10',
        'status' => 'belum_bayar',
    ]);
});

test('pembayaran orang tua page returns a successful response', function () {
    $response = $this->get('/wali/pembayaran');

    $response->assertStatus(200);
    $response->assertSee('Pembayaran &amp; Tagihan Bimbingan', false);
    $response->assertSee('Sisa Tagihan yang Perlu Dibayar');
    $response->assertSee('Total Sudah Terbayar');
    $response->assertSee('Daftar Tagihan');
    $response->assertSee('Bayar Instan');
    $response->assertSee('Tagihan Perlu Dibayar');
    $response->assertSee('Kwitansi');
});

test('pembayaran orang tua is reachable from its named route', function () {
    $this->get(route('wali.pembayaran'))->assertStatus(200);
});

test('pembayaran orang tua menampilkan data tagihan dari database', function () {
    $this->get('/wali/pembayaran')
        ->assertStatus(200)
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('SPP April 2025')
        ->assertSee('SPP Maret 2025')
        ->assertSee('SPP Februari 2025')
        ->assertSee('Rp 1.450.000')
        ->assertSee('Lunas')
        ->assertSee('Belum Lunas');
});

test('pembayaran orang tua menampilkan ringkasan sisa tagihan dari database', function () {
    // Anak 1: SPP April belum bayar (1.450.000) + SPP Maret lunas + SPP Februari lunas
    // Anak 2: SPP April belum bayar (1.450.000)
    // Total sisa = 1.450.000 + 1.450.000 = 2.900.000
    // Total terbayar = 1.450.000 + 1.450.000 = 2.900.000

    $this->get('/wali/pembayaran')
        ->assertStatus(200)
        ->assertSee('Rp 2.900.000') // sisa tagihan
        ->assertSee('Rp 2.900.000'); // total terbayar
});

test('pembayaran orang tua hanya menampilkan tagihan anak wali yang login', function () {
    // Buat user ortu lain dengan anak sendiri yang punya tagihan
    $userLain = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Budi Santoso',
        'email' => 'budi@email.com',
    ]);

    $guardianLain = Guardian::create([
        'user_id' => $userLain->id,
        'name' => 'Budi Santoso',
        'occupation' => 'Pegawai',
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
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
    ]);

    Invoice::create([
        'student_id' => $anakLain->id,
        'invoice_number' => 'INV-202504-0099',
        'type' => 'spp_bulanan',
        'description' => 'SPP April 2025 - Kelas 11 IPA',
        'amount' => 2000000,
        'due_date' => '2025-04-10',
        'status' => 'belum_bayar',
    ]);

    $this->get('/wali/pembayaran')
        ->assertStatus(200)
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('Anindya Putri Rahayu')
        ->assertDontSee('Citra Lestari')
        ->assertDontSee('INV-202504-0099')
        ->assertDontSee('Rp 2.000.000');
});

test('pembayaran orang tua dapat difilter menurut periode', function () {
    $this->get('/wali/pembayaran?periode=april-2025')
        ->assertStatus(200)
        ->assertSee('SPP April 2025')
        ->assertSee('Aisyah Putri Azzahra');
});

test('pembayaran orang tua menampilkan aksi sesuai status tagihan', function () {
    $this->get('/wali/pembayaran')
        ->assertStatus(200)
        ->assertSee('Bayar Instan') // untuk tagihan belum lunas
        ->assertSee('Kwitansi'); // untuk tagihan lunas
});
