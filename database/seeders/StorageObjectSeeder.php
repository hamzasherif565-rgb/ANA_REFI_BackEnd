<?php

namespace Database\Seeders;

use App\Models\StorageObject;
use Illuminate\Database\Seeder;

class StorageObjectSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - StorageObject::query()->count());
        if ($remaining > 0) {
            StorageObject::factory()->count($remaining)->create();
        }
    }
}
