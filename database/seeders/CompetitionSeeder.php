<?php

namespace Database\Seeders;

use App\Models\Competition;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Competition::query()->count());
        if ($remaining > 0) {
            Competition::factory()->count($remaining)->create();
        }
    }
}
