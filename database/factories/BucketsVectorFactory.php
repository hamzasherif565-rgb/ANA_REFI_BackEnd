<?php

namespace Database\Factories;

use App\Models\BucketsVector;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BucketsVector> */
class BucketsVectorFactory extends Factory
{
    protected $model = BucketsVector::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'type' => fake()->randomElement(array (
  0 => 'STANDARD',
  1 => 'ANALYTICS',
  2 => 'VECTOR',
)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
