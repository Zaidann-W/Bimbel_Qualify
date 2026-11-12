<?php

namespace Database\Factories;

use App\Models\CashAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expense_category_id' => ExpenseCategory::factory(),
            'cash_account_id' => CashAccount::factory(),
            'amount' => fake()->randomElement([25000, 50000, 125000, 350000]),
            'spent_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'description' => fake()->sentence(5),
            'recorded_by' => null,
            'receipt_file_id' => null,
        ];
    }
}
