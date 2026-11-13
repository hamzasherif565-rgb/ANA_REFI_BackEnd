<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bucket extends Model
{
    use HasFactory;

    protected $table = 'storage_buckets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'owner', 'created_at', 'updated_at', 'public', 'avif_autodetection', 'file_size_limit', 'allowed_mime_types', 'owner_id', 'type', 'versioning_status', 'lifecycle_configuration', 'lifecycle_configuration_generation'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'public' => 'boolean',
  'avif_autodetection' => 'boolean',
  'allowed_mime_types' => 'array',
  'lifecycle_configuration' => 'array',
);
    }

    public function objectByBucketId(): HasMany { return $this->hasMany(\App\Models\StorageObject::class, 'bucket_id', 'id'); }

    public function s3MultipartUploadByBucketId(): HasMany { return $this->hasMany(\App\Models\S3MultipartUpload::class, 'bucket_id', 'id'); }

    public function s3MultipartUploadsPartByBucketId(): HasMany { return $this->hasMany(\App\Models\S3MultipartUploadsPart::class, 'bucket_id', 'id'); }
}
