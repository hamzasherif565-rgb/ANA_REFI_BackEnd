<?php

namespace Database\Seeders;

use App\Models\VolunteerServiceRequest;
use Illuminate\Database\Seeder;

class VolunteerServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - VolunteerServiceRequest::query()->count());
        if ($remaining > 0) {
            VolunteerServiceRequest::factory()->count($remaining)->create();
        }
    }
}
