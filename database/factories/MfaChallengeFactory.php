<?php

namespace Database\Factories;

use App\Models\MfaChallenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MfaChallenge> */
class MfaChallengeFactory extends Factory
{
    protected $model = MfaChallenge::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'factor_id' => \App\Models\MfaFactor::factory(),
            'created_at' => now(),
            'verified_at' => null,
            'ip_address' => fake()->ipv4(),
            'otp_code' => null,
            'web_authn_session_data' => null,
        ];
    }
}
