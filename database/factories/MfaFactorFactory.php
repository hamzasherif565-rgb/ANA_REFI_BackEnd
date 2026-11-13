<?php

namespace Database\Factories;

use App\Models\MfaFactor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MfaFactor> */
class MfaFactorFactory extends Factory
{
    protected $model = MfaFactor::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'friendly_name' => null,
            'factor_type' => fake()->randomElement(array (
  0 => 'totp',
  1 => 'webauthn',
  2 => 'phone',
  3 => 'recovery_code',
)),
            'status' => fake()->randomElement(array (
  0 => 'unverified',
  1 => 'verified',
)),
            'created_at' => now(),
            'updated_at' => now(),
            'secret' => null,
            'phone' => null,
            'last_challenged_at' => null,
            'web_authn_credential' => null,
            'web_authn_aaguid' => null,
            'last_webauthn_challenge_data' => null,
        ];
    }
}
