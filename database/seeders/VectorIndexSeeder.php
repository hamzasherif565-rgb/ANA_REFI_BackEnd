<?php

namespace Database\Seeders;

use App\Models\VectorIndex;
use Illuminate\Database\Seeder;

class VectorIndexSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - VectorIndex::query()->count());
        if ($remaining > 0) {
            VectorIndex::factory()->count($remaining)->create();
        }
    }
}
