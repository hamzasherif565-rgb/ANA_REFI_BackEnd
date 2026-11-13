<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Notification::query()->count());
        if ($remaining > 0) {
            Notification::factory()->count($remaining)->create();
        }
    }
}
