<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - Booking::query()->count());
        if ($remaining > 0) {
            Booking::factory()->count($remaining)->create();
        }
    }
}
