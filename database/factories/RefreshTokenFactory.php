<?php

namespace Database\Factories;

use App\Models\RefreshToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RefreshToken> */
class RefreshTokenFactory extends Factory
{
    protected $model = RefreshToken::class;

    public function definition(): array
    {
        return [
            'instance_id' => null,
            'token' => null,
            'user_id' => null,
            'revoked' => null,
            'created_at' => null,
            'updated_at' => null,
            'parent' => null,
            'session_id' => null,
        ];
    }
}
