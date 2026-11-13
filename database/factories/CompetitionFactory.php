<?php

namespace Database\Factories;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Competition> */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'host_id' => null,
            'title' => fake()->sentence(),
            'title_en' => null,
            'description' => null,
            'description_en' => null,
            'category' => fake()->sentence(),
            'location' => fake()->sentence(),
            'starts_at' => now(),
            'ends_at' => now(),
            'prize_amount' => 0,
            'entry_fee' => 0,
            'capacity' => 30,
            'participants_count' => 0,
            'image_url' => null,
            'status' => fake()->randomElement(array (
  0 => 'draft',
  1 => 'upcoming',
  2 => 'open',
  3 => 'closed',
  4 => 'completed',
  5 => 'cancelled',
)),
            'featured' => false,
            'coming_soon' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
