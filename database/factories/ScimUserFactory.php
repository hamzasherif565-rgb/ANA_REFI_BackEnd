<?php

namespace Database\Factories;

use App\Models\ScimUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScimUser> */
class ScimUserFactory extends Factory
{
    protected $model = ScimUser::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'sso_provider_id' => \App\Models\SsoProvider::factory(),
            'user_id' => null,
            'resource' => [],
            'user_name' => fake()->word(),
            'external_id' => null,
            'active' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
