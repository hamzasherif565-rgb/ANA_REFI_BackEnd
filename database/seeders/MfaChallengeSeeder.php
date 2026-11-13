<?php

namespace Database\Seeders;

use App\Models\MfaChallenge;
use Illuminate\Database\Seeder;

class MfaChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - MfaChallenge::query()->count());
        if ($remaining > 0) {
            MfaChallenge::factory()->count($remaining)->create();
        }
    }
}
