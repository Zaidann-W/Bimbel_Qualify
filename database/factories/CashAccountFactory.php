<?php

namespace Database\Factories;

use App\Models\CashAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashAccount>
 */
class CashAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CashAccount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Laci Kas ('.fake()->unique()->randomLetter().')',
            'type' => 'tunai',
            'bank_name' => null,
            'account_number' => null,
            'payment_methods' => 'cash,tunai',
            'opening_balance' => 0,
            'is_active' => true,
        ];
    }

    /**
     * Akun kas fisik di laci kasir.
     */
    public function tunai(): static
    {
        return $this->state([
            'name' => 'Laci Kas (Fisik)',
            'type' => 'tunai',
            'payment_methods' => 'cash,tunai',
        ]);
    }

    /**
     * Akun rekening bank yang menerima VA / transfer / QRIS.
     */
    public function bank(): static
    {
        return $this->state([
            'name' => 'Bank BCA',
            'type' => 'bank',
            'bank_name' => 'BCA',
            'account_number' => '822-0918-201',
            'payment_methods' => 'va_bca,transfer_bank,qris',
        ]);
    }
}
