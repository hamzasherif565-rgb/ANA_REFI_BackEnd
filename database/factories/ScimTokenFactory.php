<?php

namespace Database\Factories;

use App\Models\ScimToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScimToken> */
class ScimTokenFactory extends Factory
{
    protected $model = ScimToken::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'sso_provider_id' => \App\Models\SsoProvider::factory(),
            'token_hash' => fake()->unique()->sha256(),
            'prefix' => fake()->word(),
            'created_at' => now(),
            'expires_at' => null,
            'revoked_at' => null,
            'last_used_at' => null,
        ];
    }
}
