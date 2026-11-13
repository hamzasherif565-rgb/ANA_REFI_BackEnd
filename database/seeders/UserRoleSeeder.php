<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - UserRole::query()->count());
        if ($remaining > 0) {
            UserRole::factory()->count($remaining)->create();
        }
    }
}
