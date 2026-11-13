<?php

namespace Database\Factories;

use App\Models\Migration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Migration> */
class MigrationFactory extends Factory
{
    protected $model = Migration::class;

    public function definition(): array
    {
        return [
            'id' => fake()->unique()->numberBetween(1, 2147483647),
            'name' => fake()->unique()->word(),
            'hash' => fake()->word(),
            'executed_at' => null,
        ];
    }
}
