<?php

namespace Database\Factories;

use App\Models\AuthSchemaMigration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuthSchemaMigration> */
class AuthSchemaMigrationFactory extends Factory
{
    protected $model = AuthSchemaMigration::class;

    public function definition(): array
    {
        return [
            'version' => fake()->word(),
        ];
    }
}
