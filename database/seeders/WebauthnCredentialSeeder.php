<?php

namespace Database\Seeders;

use App\Models\WebauthnCredential;
use Illuminate\Database\Seeder;

class WebauthnCredentialSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - WebauthnCredential::query()->count());
        if ($remaining > 0) {
            WebauthnCredential::factory()->count($remaining)->create();
        }
    }
}
