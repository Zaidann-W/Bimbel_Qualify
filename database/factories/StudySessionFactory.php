<?php

namespace Database\Factories;

use App\Models\StudySession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudySession>
 */
class StudySessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'category_badge' => '12 Super Intensif UTBK SNBT',
            'session_number' => fake()->numberBetween(1, 24),
            'total_sessions' => 24,
            'date' => today(),
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration_minutes' => 90,
            'room_name' => 'Ruangan Eureka (Lantai 2)',
            'room_capacity' => 15,
            'room_facilities' => 'Smart Board + AC',
            'target_ptn' => 'Top 3 PTN',
            'tutor_name' => 'Kak Hendra Wijaya, M.Sc',
            'tutor_rating' => 4.96,
            'tutor_reviews_count' => 320,
            'rfid_checkin_start_time' => '16:15:00',
        ];
    }
}
