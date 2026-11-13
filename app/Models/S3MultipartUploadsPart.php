<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class S3MultipartUploadsPart extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'storage_s3_multipart_uploads_parts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'upload_id', 'size', 'part_number', 'bucket_id', 'key', 'etag', 'owner_id', 'version', 'created_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
);
    }

    public function bucketByBucketId(): BelongsTo { return $this->belongsTo(\App\Models\Bucket::class, 'bucket_id', 'id'); }

    public function s3MultipartUploadByUploadId(): BelongsTo { return $this->belongsTo(\App\Models\S3MultipartUpload::class, 'upload_id', 'id'); }
}
