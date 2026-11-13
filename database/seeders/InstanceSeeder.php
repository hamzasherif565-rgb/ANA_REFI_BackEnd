<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Instance::query()->count());
        if ($remaining > 0) {
            Instance::factory()->count($remaining)->create();
        }
    }
}
