<?php

namespace Database\Factories;

use App\Models\SamlRelayState;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SamlRelayState> */
class SamlRelayStateFactory extends Factory
{
    protected $model = SamlRelayState::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'sso_provider_id' => \App\Models\SsoProvider::factory(),
            'request_id' => fake()->word(),
            'for_email' => null,
            'redirect_to' => null,
            'created_at' => null,
            'updated_at' => null,
            'flow_state_id' => null,
        ];
    }
}
