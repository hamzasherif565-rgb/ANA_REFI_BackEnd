<?php

namespace Database\Factories;

use App\Models\OauthClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OauthClient> */
class OauthClientFactory extends Factory
{
    protected $model = OauthClient::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'client_secret_hash' => null,
            'registration_type' => fake()->randomElement(array (
  0 => 'dynamic',
  1 => 'manual',
)),
            'redirect_uris' => 'https://example.test/'.fake()->slug(),
            'grant_types' => fake()->word(),
            'client_name' => null,
            'client_uri' => null,
            'logo_uri' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
            'client_type' => fake()->randomElement(array (
  0 => 'public',
  1 => 'confidential',
)),
            'token_endpoint_auth_method' => fake()->randomElement(array (
  0 => 'client_secret_basic',
  1 => 'client_secret_post',
  2 => 'none',
)),
        ];
    }
}
