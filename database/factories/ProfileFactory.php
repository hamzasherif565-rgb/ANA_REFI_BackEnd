<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Profile> */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'id' => \App\Models\User::factory(),
            'display_name' => null,
            'avatar_url' => null,
            'bio' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'account_type' => fake()->randomElement(array (
  0 => 'traveler',
  1 => 'host',
  2 => 'admin',
)),
            'is_blocked' => false,
            'role_selected' => false,
            'age' => null,
            'location' => null,
            'phone' => null,
            'email' => fake()->safeEmail(),
        ];
    }
}
