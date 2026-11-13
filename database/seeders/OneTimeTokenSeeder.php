<?php

namespace Database\Seeders;

use App\Models\OneTimeToken;
use Illuminate\Database\Seeder;

class OneTimeTokenSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - OneTimeToken::query()->count());
        if ($remaining > 0) {
            OneTimeToken::factory()->count($remaining)->create();
        }
    }
}
