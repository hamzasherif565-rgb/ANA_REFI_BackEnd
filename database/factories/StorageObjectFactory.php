<?php

namespace Database\Factories;

use App\Models\StorageObject;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StorageObject> */
class StorageObjectFactory extends Factory
{
    protected $model = StorageObject::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'bucket_id' => null,
            'name' => null,
            'owner' => null,
            'created_at' => null,
            'updated_at' => null,
            'last_accessed_at' => null,
            'metadata' => null,
            'path_tokens' => null,
            'version' => null,
            'owner_id' => null,
            'user_metadata' => null,
            'archived_at' => null,
            'is_delete_marker' => false,
            'is_versioned' => false,
        ];
    }
}
