<?php

namespace Database\Factories;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Favorite> */
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'item_type' => fake()->randomElement(array (
  0 => 'competition',
  1 => 'stay',
  2 => 'experience',
  3 => 'product',
)),
            'item_id' => fake()->uuid(),
            'created_at' => now(),
        ];
    }
}
