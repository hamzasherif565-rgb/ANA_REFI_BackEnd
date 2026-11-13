<?php

namespace Database\Seeders;

use App\Models\SsoDomain;
use Illuminate\Database\Seeder;

class SsoDomainSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - SsoDomain::query()->count());
        if ($remaining > 0) {
            SsoDomain::factory()->count($remaining)->create();
        }
    }
}
