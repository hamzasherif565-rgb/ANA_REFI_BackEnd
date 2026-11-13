<?php

namespace Database\Factories;

use App\Models\SsoDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SsoDomain> */
class SsoDomainFactory extends Factory
{
    protected $model = SsoDomain::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'sso_provider_id' => \App\Models\SsoProvider::factory(),
            'domain' => fake()->word(),
            'created_at' => null,
            'updated_at' => null,
        ];
    }
}
