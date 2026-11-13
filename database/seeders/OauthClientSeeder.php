<?php

namespace Database\Seeders;

use App\Models\OauthClient;
use Illuminate\Database\Seeder;

class OauthClientSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - OauthClient::query()->count());
        if ($remaining > 0) {
            OauthClient::factory()->count($remaining)->create();
        }
    }
}
