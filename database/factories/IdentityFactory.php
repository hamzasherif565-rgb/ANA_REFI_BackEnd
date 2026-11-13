<?php

namespace Database\Factories;

use App\Models\Identity;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Identity> */
class IdentityFactory extends Factory
{
    protected $model = Identity::class;

    public function definition(): array
    {
        return [
            'provider_id' => fake()->word(),
            'user_id' => \App\Models\User::factory(),
            'identity_data' => [],
            'provider' => fake()->word(),
            'last_sign_in_at' => null,
            'created_at' => null,
            'updated_at' => null,
            'email' => fake()->safeEmail(),
            'id' => fake()->uuid(),
        ];
    }
}
