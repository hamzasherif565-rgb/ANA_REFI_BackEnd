<?php

namespace Database\Factories;

use App\Models\VectorIndex;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VectorIndex> */
class VectorIndexFactory extends Factory
{
    protected $model = VectorIndex::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->name(),
            'bucket_id' => \App\Models\BucketsVector::factory(),
            'data_type' => fake()->word(),
            'dimension' => fake()->numberBetween(0, 1000),
            'distance_metric' => fake()->word(),
            'metadata_configuration' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
