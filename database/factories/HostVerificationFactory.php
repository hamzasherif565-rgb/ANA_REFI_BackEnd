<?php

namespace Database\Factories;

use App\Models\HostVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HostVerification> */
class HostVerificationFactory extends Factory
{
    protected $model = HostVerification::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'national_id' => fake()->word(),
            'id_front_path' => null,
            'id_back_path' => null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
