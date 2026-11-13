<?php

namespace Database\Seeders;

use App\Models\Identity;
use Illuminate\Database\Seeder;

class IdentitySeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Identity::query()->count());
        if ($remaining > 0) {
            Identity::factory()->count($remaining)->create();
        }
    }
}
