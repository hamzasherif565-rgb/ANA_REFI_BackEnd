<?php

namespace Database\Factories;

use App\Models\CustomOauthProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CustomOauthProvider> */
class CustomOauthProviderFactory extends Factory
{
    protected $model = CustomOauthProvider::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'provider_type' => 'oauth2',
            'identifier' => 'provider-'.fake()->unique()->numerify('####'),
            'name' => fake()->name(),
            'client_id' => fake()->word(),
            'client_secret' => fake()->word(),
            'acceptable_client_ids' => [],
            'scopes' => [],
            'pkce_enabled' => true,
            'attribute_mapping' => [],
            'authorization_params' => [],
            'enabled' => true,
            'email_optional' => false,
            'issuer' => null,
            'discovery_url' => null,
            'skip_nonce_check' => false,
            'cached_discovery' => null,
            'discovery_cached_at' => null,
            'authorization_url' => 'https://example.test/authorization_url',
            'token_url' => 'https://example.test/token_url',
            'userinfo_url' => 'https://example.test/userinfo_url',
            'jwks_uri' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'custom_claims_allowlist' => [],
        ];
    }
}
