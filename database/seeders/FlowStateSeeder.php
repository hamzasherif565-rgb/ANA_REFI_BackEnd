<?php

namespace Database\Seeders;

use App\Models\FlowState;
use Illuminate\Database\Seeder;

class FlowStateSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - FlowState::query()->count());
        if ($remaining > 0) {
            FlowState::factory()->count($remaining)->create();
        }
    }
}
