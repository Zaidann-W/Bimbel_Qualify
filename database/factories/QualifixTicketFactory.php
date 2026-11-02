<?php

namespace Database\Factories;

use App\Models\QualifixTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualifixTicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QualifixTicket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'KPR-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->sentence(6),
            'question' => fake()->paragraph(),
            'status' => fake()->randomElement(['menunggu_tentor', 'sedang_dibahas', 'selesai']),
            'submitted_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Tiket yang masih menunggu tentor.
     */
    public function menunggu(): static
    {
        return $this->state(['status' => 'menunggu_tentor']);
    }

    /**
     * Tiket yang sedang dibahas.
     */
    public function sedangDibahas(): static
    {
        return $this->state(['status' => 'sedang_dibahas']);
    }

    /**
     * Tiket yang telah selesai.
     */
    public function selesai(): static
    {
        return $this->state(['status' => 'selesai']);
    }
}
