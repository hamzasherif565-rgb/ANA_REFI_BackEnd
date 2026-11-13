<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'listing_id' => \App\Models\Listing::factory(),
            'user_id' => \App\Models\User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => null,
            'image_url' => null,
            'created_at' => now(),
        ];
    }
}
