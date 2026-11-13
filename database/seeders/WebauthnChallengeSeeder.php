<?php

namespace Database\Seeders;

use App\Models\WebauthnChallenge;
use Illuminate\Database\Seeder;

class WebauthnChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - WebauthnChallenge::query()->count());
        if ($remaining > 0) {
            WebauthnChallenge::factory()->count($remaining)->create();
        }
    }
}
