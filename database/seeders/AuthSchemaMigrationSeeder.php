<?php

namespace Database\Seeders;

use App\Models\AuthSchemaMigration;
use Illuminate\Database\Seeder;

class AuthSchemaMigrationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - AuthSchemaMigration::query()->count());
        if ($remaining > 0) {
            AuthSchemaMigration::factory()->count($remaining)->create();
        }
    }
}
