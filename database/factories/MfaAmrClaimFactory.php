<?php

namespace Database\Factories;

use App\Models\MfaAmrClaim;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MfaAmrClaim> */
class MfaAmrClaimFactory extends Factory
{
    protected $model = MfaAmrClaim::class;

    public function definition(): array
    {
        return [
            'session_id' => \App\Models\Session::factory(),
            'created_at' => now(),
            'updated_at' => now(),
            'authentication_method' => fake()->word(),
            'id' => fake()->uuid(),
        ];
    }
}
