<?php

namespace Database\Seeders;

use App\Models\MfaRecoveryCodeSet;
use Illuminate\Database\Seeder;

class MfaRecoveryCodeSetSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - MfaRecoveryCodeSet::query()->count());
        if ($remaining > 0) {
            MfaRecoveryCodeSet::factory()->count($remaining)->create();
        }
    }
}
