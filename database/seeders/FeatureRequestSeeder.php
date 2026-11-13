<?php

namespace Database\Seeders;

use App\Models\FeatureRequest;
use Illuminate\Database\Seeder;

class FeatureRequestSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - FeatureRequest::query()->count());
        if ($remaining > 0) {
            FeatureRequest::factory()->count($remaining)->create();
        }
    }
}
