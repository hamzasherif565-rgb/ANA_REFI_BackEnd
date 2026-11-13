<?php

namespace Database\Seeders;

use App\Models\MfaAmrClaim;
use Illuminate\Database\Seeder;

class MfaAmrClaimSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - MfaAmrClaim::query()->count());
        if ($remaining > 0) {
            MfaAmrClaim::factory()->count($remaining)->create();
        }
    }
}
