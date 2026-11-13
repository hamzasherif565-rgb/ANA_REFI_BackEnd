<?php

namespace Database\Seeders;

use App\Models\SamlRelayState;
use Illuminate\Database\Seeder;

class SamlRelayStateSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - SamlRelayState::query()->count());
        if ($remaining > 0) {
            SamlRelayState::factory()->count($remaining)->create();
        }
    }
}
