<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - ContactMessage::query()->count());
        if ($remaining > 0) {
            ContactMessage::factory()->count($remaining)->create();
        }
    }
}
