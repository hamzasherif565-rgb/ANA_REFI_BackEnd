<?php

namespace Database\Seeders;

use App\Models\OauthClientState;
use Illuminate\Database\Seeder;

class OauthClientStateSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - OauthClientState::query()->count());
        if ($remaining > 0) {
            OauthClientState::factory()->count($remaining)->create();
        }
    }
}
