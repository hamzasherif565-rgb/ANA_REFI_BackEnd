<?php

namespace Database\Factories;

use App\Models\WebauthnCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WebauthnCredential> */
class WebauthnCredentialFactory extends Factory
{
    protected $model = WebauthnCredential::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'credential_id' => fake()->sha256(),
            'public_key' => fake()->sha256(),
            'attestation_type' => '',
            'aaguid' => null,
            'sign_count' => 0,
            'transports' => [],
            'backup_eligible' => false,
            'backed_up' => false,
            'friendly_name' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'last_used_at' => null,
        ];
    }
}
