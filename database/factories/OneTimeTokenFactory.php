<?php

namespace Database\Factories;

use App\Models\OneTimeToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OneTimeToken> */
class OneTimeTokenFactory extends Factory
{
    protected $model = OneTimeToken::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'token_type' => fake()->randomElement(array (
  0 => 'confirmation_token',
  1 => 'reauthentication_token',
  2 => 'recovery_token',
  3 => 'email_change_token_new',
  4 => 'email_change_token_current',
  5 => 'phone_change_token',
)),
            'token_hash' => fake()->sha256(),
            'relates_to' => fake()->word(),
            'created_at' => now(),
            'updated_at' => now(),
            'expires_at' => null,
        ];
    }
}
