<?php

namespace Database\Factories;

use App\Models\SessionMaterial;
use App\Models\StudySession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SessionMaterial>
 */
class SessionMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'study_session_id' => StudySession::factory(),
            'title' => fake()->sentence(4),
            'file_type' => 'PDF',
            'file_size' => fake()->randomFloat(1, 0.5, 8).' MB',
            'metadata' => null,
            'file_path' => fake()->url(),
        ];
    }

    /**
     * Materi berupa slide yang diakses secara online.
     */
    public function slide(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'Slide',
            'file_size' => 'Akses Online',
            'metadata' => 'Live Anotasi saat sesi',
        ]);
    }
}
