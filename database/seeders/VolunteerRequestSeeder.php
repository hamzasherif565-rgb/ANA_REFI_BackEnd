<?php

namespace Database\Seeders;

use App\Models\VolunteerRequest;
use Illuminate\Database\Seeder;

class VolunteerRequestSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - VolunteerRequest::query()->count());
        if ($remaining > 0) {
            VolunteerRequest::factory()->count($remaining)->create();
        }
    }
}
