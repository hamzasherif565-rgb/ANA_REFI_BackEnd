<?php

namespace Database\Seeders;

use App\Models\Secret;
use Illuminate\Database\Seeder;

class SecretSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Secret::query()->count());
        if ($remaining > 0) {
            Secret::factory()->count($remaining)->create();
        }
    }
}
