<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class S3MultipartUpload extends Model
{
    use HasFactory;

    protected $table = 'storage_s3_multipart_uploads';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'in_progress_size', 'upload_signature', 'bucket_id', 'key', 'version', 'owner_id', 'created_at', 'user_metadata', 'metadata'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'user_metadata' => 'array',
  'metadata' => 'array',
);
    }

    public function bucketByBucketId(): BelongsTo { return $this->belongsTo(\App\Models\Bucket::class, 'bucket_id', 'id'); }

    public function s3MultipartUploadsPartByUploadId(): HasMany { return $this->hasMany(\App\Models\S3MultipartUploadsPart::class, 'upload_id', 'id'); }
}
