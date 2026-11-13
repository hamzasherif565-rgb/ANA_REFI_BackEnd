<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Review::query()->count());
        if ($remaining > 0) {
            Review::factory()->count($remaining)->create();
        }
    }
}
