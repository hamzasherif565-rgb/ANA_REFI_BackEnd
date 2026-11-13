<?php

namespace Database\Seeders;

use App\Models\CommunityPost;
use Illuminate\Database\Seeder;

class CommunityPostSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - CommunityPost::query()->count());
        if ($remaining > 0) {
            CommunityPost::factory()->count($remaining)->create();
        }
    }
}
