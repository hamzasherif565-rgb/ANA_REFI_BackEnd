<?php

namespace Database\Seeders;

use App\Models\MfaRecoveryCode;
use Illuminate\Database\Seeder;

class MfaRecoveryCodeSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - MfaRecoveryCode::query()->count());
        if ($remaining > 0) {
            MfaRecoveryCode::factory()->count($remaining)->create();
        }
    }
}
