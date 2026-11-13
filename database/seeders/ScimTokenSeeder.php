<?php

namespace Database\Seeders;

use App\Models\ScimToken;
use Illuminate\Database\Seeder;

class ScimTokenSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - ScimToken::query()->count());
        if ($remaining > 0) {
            ScimToken::factory()->count($remaining)->create();
        }
    }
}
