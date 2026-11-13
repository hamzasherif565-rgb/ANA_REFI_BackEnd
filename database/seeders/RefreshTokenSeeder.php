<?php

namespace Database\Seeders;

use App\Models\RefreshToken;
use Illuminate\Database\Seeder;

class RefreshTokenSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - RefreshToken::query()->count());
        if ($remaining > 0) {
            RefreshToken::factory()->count($remaining)->create();
        }
    }
}
