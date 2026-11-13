<?php

namespace Database\Factories;

use App\Models\WebauthnChallenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WebauthnChallenge> */
class WebauthnChallengeFactory extends Factory
{
    protected $model = WebauthnChallenge::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => null,
            'challenge_type' => fake()->randomElement(array (
  0 => 'signup',
  1 => 'registration',
  2 => 'authentication',
)),
            'session_data' => [],
            'created_at' => now(),
            'expires_at' => now(),
        ];
    }
}
