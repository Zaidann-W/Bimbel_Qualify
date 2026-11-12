<?php

namespace Database\Factories;

use App\Models\CashAccount;
use App\Models\CashSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashSession>
 */
class CashSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CashSession::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cash_account_id' => CashAccount::factory()->tunai(),
            'opened_by' => User::factory()->state(['role' => 'admin']),
            'opened_at' => fake()->dateTimeBetween('-1 day', 'now'),
            'opening_balance' => 100000,
            'closed_by' => null,
            'closed_at' => null,
            'counted_balance' => null,
            'system_balance' => null,
            'difference' => null,
            'note' => null,
            'status' => 'aktif',
        ];
    }

    /**
     * Sesi kas yang sudah ditutup.
     */
    public function ditutup(): static
    {
        return $this->state(function () {
            $system = fake()->randomElement([150000, 250000, 500000]);

            return [
                'status' => 'ditutup',
                'closed_by' => User::factory()->state(['role' => 'admin']),
                'closed_at' => now(),
                'system_balance' => $system,
                'counted_balance' => $system,
                'difference' => 0,
            ];
        });
    }
}
