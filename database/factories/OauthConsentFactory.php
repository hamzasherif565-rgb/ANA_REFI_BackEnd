<?php

namespace Database\Factories;

use App\Models\OauthConsent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OauthConsent> */
class OauthConsentFactory extends Factory
{
    protected $model = OauthConsent::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'client_id' => \App\Models\OauthClient::factory(),
            'scopes' => fake()->sentence(),
            'granted_at' => now(),
            'revoked_at' => null,
        ];
    }
}
