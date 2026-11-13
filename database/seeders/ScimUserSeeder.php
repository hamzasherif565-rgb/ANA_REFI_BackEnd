<?php

namespace Database\Seeders;

use App\Models\ScimUser;
use Illuminate\Database\Seeder;

class ScimUserSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - ScimUser::query()->count());
        if ($remaining > 0) {
            ScimUser::factory()->count($remaining)->create();
        }
    }
}
