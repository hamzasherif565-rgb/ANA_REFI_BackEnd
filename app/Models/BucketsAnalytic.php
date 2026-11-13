<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BucketsAnalytic extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'storage_buckets_analytics';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['name', 'type', 'format', 'created_at', 'updated_at', 'id', 'deleted_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'deleted_at' => 'datetime',
);
    }
}
