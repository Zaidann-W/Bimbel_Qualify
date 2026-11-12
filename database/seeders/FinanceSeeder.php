<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinanceSeeder extends Seeder
{
    /**
     * Data awal modul keuangan: akun kas & kategori pengeluaran.
     */
    public function run(): void
    {
        CashAccount::updateOrCreate(['name' => 'Laci Kas (Fisik)'], [
            'type' => 'tunai',
            'payment_methods' => 'cash,tunai',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        CashAccount::updateOrCreate(['name' => 'Bank BCA'], [
            'type' => 'bank',
            'bank_name' => 'BCA',
            'account_number' => '822-0918-201',
            'payment_methods' => 'va_bca,transfer_bank,qris',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        $kategori = [
            ['name' => 'Operasional Harian', 'description' => 'Kebutuhan harian kantor & kelas'],
            ['name' => 'Honorarium Pengajar', 'description' => 'Pembayaran honor tentor & guru'],
            ['name' => 'Percetakan & ATK', 'description' => 'Modul, soal, alat tulis kantor'],
            ['name' => 'Sewa & Utilitas', 'description' => 'Sewa tempat, listrik, air, internet'],
            ['name' => 'Lainnya', 'description' => 'Pengeluaran di luar kategori utama'],
        ];

        foreach ($kategori as $item) {
            ExpenseCategory::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                ['name' => $item['name'], 'description' => $item['description'], 'is_active' => true]
            );
        }
    }
}
