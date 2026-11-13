<?php

namespace Database\Factories;

use App\Models\MfaRecoveryCodeSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MfaRecoveryCodeSet> */
class MfaRecoveryCodeSetFactory extends Factory
{
    protected $model = MfaRecoveryCodeSet::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'mfa_factor_id' => \App\Models\MfaFactor::factory(),
            'failed_verification_count' => 0,
            'verification_locked_until' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
