<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - User::query()->count());
        if ($remaining > 0) {
            User::factory()->count($remaining)->create();
        }
    }
}
