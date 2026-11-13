<?php

namespace Database\Seeders;

use App\Models\S3MultipartUploadsPart;
use Illuminate\Database\Seeder;

class S3MultipartUploadsPartSeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - S3MultipartUploadsPart::query()->count());
        if ($remaining > 0) {
            S3MultipartUploadsPart::factory()->count($remaining)->create();
        }
    }
}
