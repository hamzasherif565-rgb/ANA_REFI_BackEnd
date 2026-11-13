<?php

namespace Database\Seeders;

use App\Models\MfaFactor;
use Illuminate\Database\Seeder;

class MfaFactorSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - MfaFactor::query()->count());
        if ($remaining > 0) {
            MfaFactor::factory()->count($remaining)->create();
        }
    }
}
