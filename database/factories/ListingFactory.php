<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Listing> */
class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'host_id' => \App\Models\User::factory(),
            'type' => fake()->randomElement(array (
  0 => 'stay',
  1 => 'experience',
  2 => 'product',
  3 => 'competition',
)),
            'title' => fake()->sentence(),
            'title_en' => null,
            'description' => null,
            'description_en' => null,
            'category' => null,
            'price' => 0,
            'discount' => 0,
            'capacity' => null,
            'location' => null,
            'images' => [],
            'starts_at' => null,
            'ends_at' => null,
            'is_featured' => false,
            'status' => fake()->randomElement(array (
  0 => 'active',
  1 => 'blocked',
  2 => 'draft',
)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
