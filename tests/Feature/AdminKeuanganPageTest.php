<?php

use App\Models\CashAccount;
use App\Models\CashSession;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'status' => 'aktif']));
});

test('keuangan returns a successful response', function () {
    $response = $this->get('/admin/keuangan');

    $response->assertStatus(200);
    $response->assertSee('Pencatatan Keuangan Harian');
    $response->assertSee('Arus kas bimbel, pembayaran SPP siswa, dan pengeluaran operasional yang tercatat.', false);
    $response->assertSee('Buku Kas');
    $response->assertSee('Kas &amp; Rekening', false);
    $response->assertSee('Tagihan SPP Belum Lunas', false);
});

test('keuangan is reachable from its named route', function () {
    $this->get(route('admin.keuangan'))->assertStatus(200);
});

test('keuangan menu is active on the page', function () {
    $this->get(route('admin.keuangan'))
        ->assertStatus(200)
        ->assertSee('Keuangan &amp; Transaksi', false);
});

test('keuangan shows ledger tabs and empty states without data', function () {
    $response = $this->get(route('admin.keuangan'));

    $response->assertStatus(200);
    $response->assertSee('Semua');
    $response->assertSee('Pemasukan');
    $response->assertSee('Pengeluaran');
    $response->assertSee('Tidak ada transaksi yang cocok');
    $response->assertSee('Tidak ada tagihan yang belum lunas');
    $response->assertSee('Tidak Ada Jatuh Tempo');
});

test('keuangan lists recorded expenses from the database', function () {
    $akun = CashAccount::factory()->tunai()->create();
    $kategori = ExpenseCategory::factory()->create(['name' => 'Percetakan dan ATK']);

    Expense::factory()->create([
        'expense_category_id' => $kategori->id,
        'cash_account_id' => $akun->id,
        'amount' => 150000,
        'description' => 'Cetak modul tryout',
        'spent_at' => now(),
    ]);

    $response = $this->get(route('admin.keuangan'));

    $response->assertStatus(200);
    $response->assertSee('Cetak modul tryout');
    $response->assertSee('Percetakan dan ATK');
    $response->assertSee('Rp 150.000');
    $response->assertSee('Laci Kas (Fisik)');
});

test('keuangan filters the ledger by direction on the server', function () {
    $akun = CashAccount::factory()->tunai()->create();
    $kategori = ExpenseCategory::factory()->create();

    Expense::factory()->create([
        'expense_category_id' => $kategori->id,
        'cash_account_id' => $akun->id,
        'description' => 'Biaya listrik kelas',
    ]);

    $this->get(route('admin.keuangan', ['arah' => 'keluar']))
        ->assertStatus(200)
        ->assertSee('Biaya listrik kelas')
        ->assertDontSee('Tidak ada transaksi yang cocok');

    $this->get(route('admin.keuangan', ['arah' => 'masuk']))
        ->assertStatus(200)
        ->assertDontSee('Biaya listrik kelas');
});

test('keuangan searches the ledger from the server', function () {
    $akun = CashAccount::factory()->tunai()->create();
    $kategori = ExpenseCategory::factory()->create();

    Expense::factory()->create([
        'expense_category_id' => $kategori->id,
        'cash_account_id' => $akun->id,
        'description' => 'Sewa proyektor bulanan',
    ]);

    $this->get(route('admin.keuangan', ['q' => 'proyektor']))
        ->assertStatus(200)
        ->assertSee('Sewa proyektor bulanan');

    $this->get(route('admin.keuangan', ['q' => 'kata-yang-tidak-ada']))
        ->assertStatus(200)
        ->assertSee('Tidak ada transaksi yang cocok');
});

test('keuangan shows the active cash session status', function () {
    $this->get(route('admin.keuangan'))
        ->assertStatus(200)
        ->assertSee('Status Kasir: BELUM DIBUKA');
});

test('keuangan shows the open cash session and its cashier', function () {
    $sesi = CashSession::factory()->create(['opened_at' => now()->subHour()]);

    $this->get(route('admin.keuangan'))
        ->assertStatus(200)
        ->assertSee('Status Kasir: AKTIF')
        ->assertSee($sesi->opener->name);
});
