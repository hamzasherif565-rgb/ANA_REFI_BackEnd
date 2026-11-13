<?php

namespace Database\Seeders;

use App\Models\Listing;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Listing::query()->count());
        if ($remaining > 0) {
            Listing::factory()->count($remaining)->create();
        }
    }
}
