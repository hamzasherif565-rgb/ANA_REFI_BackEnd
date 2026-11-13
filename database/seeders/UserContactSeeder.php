<?php

namespace Database\Seeders;

use App\Models\UserContact;
use Illuminate\Database\Seeder;

class UserContactSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - UserContact::query()->count());
        if ($remaining > 0) {
            UserContact::factory()->count($remaining)->create();
        }
    }
}
