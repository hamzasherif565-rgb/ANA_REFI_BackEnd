<?php

namespace Database\Seeders;

use App\Models\Bucket;
use Illuminate\Database\Seeder;

class BucketSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Bucket::query()->count());
        if ($remaining > 0) {
            Bucket::factory()->count($remaining)->create();
        }
    }
}
