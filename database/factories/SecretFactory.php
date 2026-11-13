<?php

namespace Database\Factories;

use App\Models\Secret;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Secret> */
class SecretFactory extends Factory
{
    protected $model = Secret::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => null,
            'description' => '',
            'secret' => fake()->word(),
            'key_id' => null,
            'nonce' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
