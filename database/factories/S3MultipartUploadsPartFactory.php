<?php

namespace Database\Factories;

use App\Models\S3MultipartUploadsPart;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<S3MultipartUploadsPart> */
class S3MultipartUploadsPartFactory extends Factory
{
    protected $model = S3MultipartUploadsPart::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'upload_id' => \App\Models\S3MultipartUpload::factory(),
            'size' => 0,
            'part_number' => fake()->numberBetween(0, 1000),
            'bucket_id' => \App\Models\Bucket::factory(),
            'key' => fake()->word(),
            'etag' => fake()->word(),
            'owner_id' => null,
            'version' => fake()->word(),
            'created_at' => now(),
        ];
    }
}
