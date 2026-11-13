<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VectorIndex extends Model
{
    use HasFactory;

    protected $table = 'storage_vector_indexes';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'bucket_id', 'data_type', 'dimension', 'distance_metric', 'metadata_configuration', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'metadata_configuration' => 'array',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function bucketsVectorByBucketId(): BelongsTo { return $this->belongsTo(\App\Models\BucketsVector::class, 'bucket_id', 'id'); }
}
