<?php

namespace Database\Seeders;

use App\Models\HostVerification;
use Illuminate\Database\Seeder;

class HostVerificationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - HostVerification::query()->count());
        if ($remaining > 0) {
            HostVerification::factory()->count($remaining)->create();
        }
    }
}
