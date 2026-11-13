<?php

namespace Database\Seeders;

use App\Models\OauthConsent;
use Illuminate\Database\Seeder;

class OauthConsentSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - OauthConsent::query()->count());
        if ($remaining > 0) {
            OauthConsent::factory()->count($remaining)->create();
        }
    }
}
