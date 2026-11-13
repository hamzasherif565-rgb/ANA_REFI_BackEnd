<?php

namespace Database\Factories;

use App\Models\BucketsAnalytic;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BucketsAnalytic> */
class BucketsAnalyticFactory extends Factory
{
    protected $model = BucketsAnalytic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'type' => fake()->randomElement(array (
  0 => 'STANDARD',
  1 => 'ANALYTICS',
  2 => 'VECTOR',
)),
            'format' => 'ICEBERG',
            'created_at' => now(),
            'updated_at' => now(),
            'id' => fake()->uuid(),
            'deleted_at' => null,
        ];
    }
}
