<?php

namespace Database\Seeders;

use App\Models\SamlProvider;
use Illuminate\Database\Seeder;

class SamlProviderSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - SamlProvider::query()->count());
        if ($remaining > 0) {
            SamlProvider::factory()->count($remaining)->create();
        }
    }
}
