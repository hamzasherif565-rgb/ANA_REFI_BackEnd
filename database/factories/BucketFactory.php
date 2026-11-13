<?php

namespace Database\Factories;

use App\Models\Bucket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Bucket> */
class BucketFactory extends Factory
{
    protected $model = Bucket::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => 'bucket-'.fake()->uuid(),
            'owner' => null,
            'created_at' => null,
            'updated_at' => null,
            'public' => null,
            'avif_autodetection' => null,
            'file_size_limit' => null,
            'allowed_mime_types' => null,
            'owner_id' => null,
            'type' => fake()->randomElement(array (
  0 => 'STANDARD',
  1 => 'ANALYTICS',
  2 => 'VECTOR',
)),
            'versioning_status' => fake()->randomElement(array (
  0 => 'DISABLED',
)),
            'lifecycle_configuration' => null,
            'lifecycle_configuration_generation' => null,
        ];
    }
}
