<?php

namespace Database\Seeders;

use App\Models\BucketsAnalytic;
use Illuminate\Database\Seeder;

class BucketsAnalyticSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - BucketsAnalytic::query()->count());
        if ($remaining > 0) {
            BucketsAnalytic::factory()->count($remaining)->create();
        }
    }
}
