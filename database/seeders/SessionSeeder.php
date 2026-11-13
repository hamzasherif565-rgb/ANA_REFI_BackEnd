<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Session::query()->count());
        if ($remaining > 0) {
            Session::factory()->count($remaining)->create();
        }
    }
}
