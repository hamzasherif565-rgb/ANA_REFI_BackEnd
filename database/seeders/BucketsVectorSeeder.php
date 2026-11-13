<?php

namespace Database\Seeders;

use App\Models\BucketsVector;
use Illuminate\Database\Seeder;

class BucketsVectorSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - BucketsVector::query()->count());
        if ($remaining > 0) {
            BucketsVector::factory()->count($remaining)->create();
        }
    }
}
