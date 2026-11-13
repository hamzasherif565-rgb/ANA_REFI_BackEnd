<?php

namespace Database\Factories;

use App\Models\OauthClientState;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OauthClientState> */
class OauthClientStateFactory extends Factory
{
    protected $model = OauthClientState::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'provider_type' => 'oauth2',
            'code_verifier' => null,
            'created_at' => now(),
        ];
    }
}
