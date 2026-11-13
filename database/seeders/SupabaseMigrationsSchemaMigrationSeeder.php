<?php

namespace Database\Seeders;

use App\Models\SupabaseMigrationsSchemaMigration;
use Illuminate\Database\Seeder;

class SupabaseMigrationsSchemaMigrationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - SupabaseMigrationsSchemaMigration::query()->count());
        if ($remaining > 0) {
            SupabaseMigrationsSchemaMigration::factory()->count($remaining)->create();
        }
    }
}
