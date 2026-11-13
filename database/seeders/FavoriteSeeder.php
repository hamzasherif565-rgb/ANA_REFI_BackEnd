<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Favorite::query()->count());
        if ($remaining > 0) {
            Favorite::factory()->count($remaining)->create();
        }
    }
}
