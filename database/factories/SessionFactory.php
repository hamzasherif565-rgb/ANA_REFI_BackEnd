<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Session> */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'created_at' => null,
            'updated_at' => null,
            'factor_id' => null,
            'aal' => null,
            'not_after' => null,
            'refreshed_at' => null,
            'user_agent' => null,
            'ip' => null,
            'tag' => null,
            'oauth_client_id' => null,
            'refresh_token_hmac_key' => null,
            'refresh_token_counter' => null,
            'scopes' => null,
        ];
    }
}
