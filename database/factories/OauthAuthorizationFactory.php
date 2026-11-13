<?php

namespace Database\Factories;

use App\Models\OauthAuthorization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OauthAuthorization> */
class OauthAuthorizationFactory extends Factory
{
    protected $model = OauthAuthorization::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'authorization_id' => fake()->word(),
            'client_id' => \App\Models\OauthClient::factory(),
            'user_id' => null,
            'redirect_uri' => 'https://example.test/'.fake()->slug(),
            'scope' => fake()->word(),
            'state' => null,
            'resource' => null,
            'code_challenge' => null,
            'code_challenge_method' => null,
            'response_type' => fake()->randomElement(array (
  0 => 'code',
)),
            'status' => fake()->randomElement(array (
  0 => 'pending',
  1 => 'approved',
  2 => 'denied',
  3 => 'expired',
)),
            'authorization_code' => null,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(3),
            'approved_at' => null,
            'nonce' => null,
        ];
    }
}

