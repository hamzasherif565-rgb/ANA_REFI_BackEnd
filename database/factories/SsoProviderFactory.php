<?php

namespace Database\Factories;

use App\Models\SsoProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SsoProvider> */
class SsoProviderFactory extends Factory
{
    protected $model = SsoProvider::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'resource_id' => null,
            'created_at' => null,
            'updated_at' => null,
            'disabled' => null,
        ];
    }
}
