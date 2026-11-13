<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StorageObject extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'storage_objects';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'bucket_id', 'name', 'owner', 'created_at', 'updated_at', 'last_accessed_at', 'metadata', 'path_tokens', 'version', 'owner_id', 'user_metadata', 'archived_at', 'is_delete_marker', 'is_versioned'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'last_accessed_at' => 'datetime',
  'metadata' => 'array',
  'path_tokens' => 'array',
  'user_metadata' => 'array',
  'archived_at' => 'datetime',
  'is_delete_marker' => 'boolean',
  'is_versioned' => 'boolean',
);
    }

    public function bucketByBucketId(): BelongsTo { return $this->belongsTo(\App\Models\Bucket::class, 'bucket_id', 'id'); }
}
