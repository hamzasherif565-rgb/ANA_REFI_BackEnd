<?php

namespace Database\Factories;

use App\Models\CommunityPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CommunityPost> */
class CommunityPostFactory extends Factory
{
    protected $model = CommunityPost::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'listing_id' => null,
            'title' => null,
            'body' => fake()->sentence(),
            'image_url' => null,
            'created_at' => now(),
        ];
    }
}
