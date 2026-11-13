<?php

namespace Database\Seeders;

use App\Models\S3MultipartUpload;
use Illuminate\Database\Seeder;

class S3MultipartUploadSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - S3MultipartUpload::query()->count());
        if ($remaining > 0) {
            S3MultipartUpload::factory()->count($remaining)->create();
        }
    }
}
