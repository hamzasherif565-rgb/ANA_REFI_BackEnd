<?php

namespace Database\Seeders;

use App\Models\SsoProvider;
use Illuminate\Database\Seeder;

class SsoProviderSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - SsoProvider::query()->count());
        if ($remaining > 0) {
            SsoProvider::factory()->count($remaining)->create();
        }
    }
}
