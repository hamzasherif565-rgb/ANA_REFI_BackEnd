<?php

namespace Database\Seeders;

use App\Models\OauthAuthorization;
use Illuminate\Database\Seeder;

class OauthAuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - OauthAuthorization::query()->count());
        if ($remaining > 0) {
            OauthAuthorization::factory()->count($remaining)->create();
        }
    }
}
