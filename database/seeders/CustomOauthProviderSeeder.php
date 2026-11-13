<?php

namespace Database\Seeders;

use App\Models\CustomOauthProvider;
use Illuminate\Database\Seeder;

class CustomOauthProviderSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - CustomOauthProvider::query()->count());
        if ($remaining > 0) {
            CustomOauthProvider::factory()->count($remaining)->create();
        }
    }
}
