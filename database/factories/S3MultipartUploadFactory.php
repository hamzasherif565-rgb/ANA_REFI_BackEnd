<?php

namespace Database\Factories;

use App\Models\S3MultipartUpload;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<S3MultipartUpload> */
class S3MultipartUploadFactory extends Factory
{
    protected $model = S3MultipartUpload::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'in_progress_size' => 0,
            'upload_signature' => fake()->word(),
            'bucket_id' => \App\Models\Bucket::factory(),
            'key' => fake()->word(),
            'version' => fake()->word(),
            'owner_id' => null,
            'created_at' => now(),
            'user_metadata' => null,
            'metadata' => null,
        ];
    }
}
