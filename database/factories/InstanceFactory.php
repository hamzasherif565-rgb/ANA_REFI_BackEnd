<?php

namespace Database\Factories;

use App\Models\Instance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Instance> */
class InstanceFactory extends Factory
{
    protected $model = Instance::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'uuid' => null,
            'raw_base_config' => null,
            'created_at' => null,
            'updated_at' => null,
        ];
    }
}
