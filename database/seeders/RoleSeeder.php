<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk membuat akun per role + akun nonaktif untuk testing.
 *
 * CATATAN PENTING:
 * - Password di bawah ini HANYA untuk development/testing.
 * - JANGAN gunakan password ini di production.
 * - Di production, gunakan password yang kuat dan unik per akun.
 * - Sebaiknya hapus/seeder ini tidak dijalankan di production.
 */
class RoleSeeder extends Seeder
{
    /**
     * Default password untuk development (hash dari 'Qualify2025!').
     * Di production, GANTI dengan password unik per akun yang dikirim via email.
     */
    protected const DEFAULT_PASSWORD = 'Qualify2025!';

    public function run(): void
    {
        $password = Hash::make(self::DEFAULT_PASSWORD);

        // Data akun per role
        $accounts = [
            [
                'name' => 'Admin Utama',
                'email' => 'admin@qualify.test',
                'phone' => '081234567890',
                'role' => 'admin',
                'status' => 'aktif',
            ],
            [
                'name' => 'Manager Operasional',
                'email' => 'manager@qualify.test',
                'phone' => '081234567891',
                'role' => 'manager',
                'status' => 'aktif',
            ],
            [
                'name' => 'Owner Bimbel',
                'email' => 'owner@qualify.test',
                'phone' => '081234567892',
                'role' => 'owner',
                'status' => 'aktif',
            ],
            [
                'name' => 'Tentor Matematika',
                'email' => 'tentor@qualify.test',
                'phone' => '081234567893',
                'role' => 'tentor',
                'status' => 'aktif',
            ],
            [
                'name' => 'Siswa Contoh',
                'email' => 'siswa@qualify.test',
                'phone' => '081234567894',
                'role' => 'siswa',
                'status' => 'aktif',
            ],
            [
                'name' => 'Ortu/Wali Siswa',
                'email' => 'ortu@qualify.test',
                'phone' => '081234567895',
                'role' => 'ortu',
                'status' => 'aktif',
            ],
        ];

        // Buat akun aktif
        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                array_merge($account, [
                    'password' => $password,
                    'email_verified_at' => now(),
                ])
            );
        }

        // Akun nonaktif untuk testing (satu per role)
        $inactiveAccounts = [
            [
                'name' => 'Admin Nonaktif',
                'email' => 'admin.nonaktif@qualify.test',
                'phone' => '081234567900',
                'role' => 'admin',
                'status' => 'nonaktif',
            ],
            [
                'name' => 'Manager Nonaktif',
                'email' => 'manager.nonaktif@qualify.test',
                'phone' => '081234567901',
                'role' => 'manager',
                'status' => 'nonaktif',
            ],
            [
                'name' => 'Owner Nonaktif',
                'email' => 'owner.nonaktif@qualify.test',
                'phone' => '081234567902',
                'role' => 'owner',
                'status' => 'nonaktif',
            ],
            [
                'name' => 'Tentor Nonaktif',
                'email' => 'tentor.nonaktif@qualify.test',
                'phone' => '081234567903',
                'role' => 'tentor',
                'status' => 'nonaktif',
            ],
            [
                'name' => 'Siswa Nonaktif',
                'email' => 'siswa.nonaktif@qualify.test',
                'phone' => '081234567904',
                'role' => 'siswa',
                'status' => 'nonaktif',
            ],
            [
                'name' => 'Ortu Nonaktif',
                'email' => 'ortu.nonaktif@qualify.test',
                'phone' => '081234567905',
                'role' => 'ortu',
                'status' => 'nonaktif',
            ],
        ];

        foreach ($inactiveAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                array_merge($account, [
                    'password' => $password,
                    'email_verified_at' => now(),
                ])
            );
        }

        // Akun soft deleted untuk testing
        $deletedUser = User::withTrashed()->updateOrCreate(
            ['email' => 'deleted@qualify.test'],
            [
                'name' => 'Akun Terhapus',
                'phone' => '081234567906',
                'role' => 'siswa',
                'status' => 'aktif',
                'password' => $password,
                'email_verified_at' => now(),
            ]
        );
        if (! $deletedUser->trashed()) {
            $deletedUser->delete(); // Soft delete
        }

        $this->command->info('RoleSeeder selesai.');
        $this->command->info('Akun aktif: admin@qualify.test, manager@qualify.test, owner@qualify.test, tentor@qualify.test, siswa@qualify.test, ortu@qualify.test');
        $this->command->info('Akun nonaktif: *.nonaktif@qualify.test');
        $this->command->info('Akun soft deleted: deleted@qualify.test');
        $this->command->warn('Password default (development): '.self::DEFAULT_PASSWORD);
        $this->command->warn('PERINGATAN: Jangan gunakan password ini di production!');
    }
}
