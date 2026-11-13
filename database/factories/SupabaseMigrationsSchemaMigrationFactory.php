<?php

namespace Database\Factories;

use App\Models\SupabaseMigrationsSchemaMigration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupabaseMigrationsSchemaMigration> */
class SupabaseMigrationsSchemaMigrationFactory extends Factory
{
    protected $model = SupabaseMigrationsSchemaMigration::class;

    public function definition(): array
    {
        return [
            'version' => fake()->unique()->uuid(),
            'statements' => null,
            'name' => null,
            'created_by' => null,
            'idempotency_key' => null,
            'rollback' => null,
        ];
    }
}
