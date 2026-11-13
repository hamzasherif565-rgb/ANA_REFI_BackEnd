<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FeatureRequest extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_feature_requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'listing_id', 'host_id', 'amount', 'payment_method', 'payment_status', 'status', 'admin_note', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function userByHostId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'host_id', 'id'); }

    public function listingByListingId(): BelongsTo { return $this->belongsTo(\App\Models\Listing::class, 'listing_id', 'id'); }
}
