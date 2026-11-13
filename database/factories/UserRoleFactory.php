<?php

namespace Database\Factories;

use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserRole> */
class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'role' => fake()->randomElement(array (
  0 => 'user',
  1 => 'host',
  2 => 'admin',
)),
            'created_at' => now(),
        ];
    }
}
