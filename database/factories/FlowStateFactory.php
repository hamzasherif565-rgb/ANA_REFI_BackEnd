<?php

namespace Database\Factories;

use App\Models\FlowState;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FlowState> */
class FlowStateFactory extends Factory
{
    protected $model = FlowState::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => null,
            'auth_code' => null,
            'code_challenge_method' => null,
            'code_challenge' => null,
            'provider_type' => 'oauth2',
            'provider_access_token' => fake()->word(),
            'provider_refresh_token' => null,
            'created_at' => null,
            'updated_at' => null,
            'authentication_method' => fake()->word(),
            'auth_code_issued_at' => null,
            'invite_token' => null,
            'referrer' => null,
            'oauth_client_state_id' => null,
            'linking_target_id' => null,
            'email_optional' => false,
        ];
    }
}
