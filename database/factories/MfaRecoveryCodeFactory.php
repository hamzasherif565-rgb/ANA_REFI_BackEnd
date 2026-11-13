<?php

namespace Database\Factories;

use App\Models\MfaRecoveryCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MfaRecoveryCode> */
class MfaRecoveryCodeFactory extends Factory
{
    protected $model = MfaRecoveryCode::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'mfa_recovery_code_set_id' => \App\Models\MfaRecoveryCodeSet::factory(),
            'code_hash' => fake()->word(),
            'consumed_at' => null,
            'created_at' => now(),
        ];
    }
}
