<?php

namespace Database\Seeders;

use App\Models\Migration;
use Illuminate\Database\Seeder;

class MigrationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Migration::query()->count());
        if ($remaining > 0) {
            Migration::factory()->count($remaining)->create();
        }
    }
}
