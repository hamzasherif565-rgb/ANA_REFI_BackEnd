<?php

namespace Database\Factories;

use App\Models\VolunteerRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VolunteerRequest> */
class VolunteerRequestFactory extends Factory
{
    protected $model = VolunteerRequest::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => fake()->uuid(),
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'interest_area' => fake()->sentence(),
            'availability' => fake()->sentence(),
            'motivation' => fake()->sentence(),
            'status' => fake()->randomElement(array (
  0 => 'pending',
  1 => 'approved',
  2 => 'rejected',
)),
            'created_at' => now(),
            'updated_at' => now(),
            'age' => null,
            'avatar_url' => null,
            'bio' => null,
            'location' => null,
            'skills' => [],
            'languages' => [],
            'experience' => null,
            'accessibility' => null,
            'rating' => null,
        ];
    }
}
