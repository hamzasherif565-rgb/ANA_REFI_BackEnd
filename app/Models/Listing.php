<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Listing extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_listings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'host_id', 'type', 'title', 'title_en', 'description', 'description_en', 'category', 'price', 'discount', 'capacity', 'location', 'images', 'starts_at', 'ends_at', 'is_featured', 'status', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'images' => 'array',
  'starts_at' => 'datetime',
  'ends_at' => 'datetime',
  'is_featured' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function bookingByListingId(): HasMany { return $this->hasMany(\App\Models\Booking::class, 'listing_id', 'id'); }

    public function communityPostByListingId(): HasMany { return $this->hasMany(\App\Models\CommunityPost::class, 'listing_id', 'id'); }

    public function featureRequestByListingId(): HasMany { return $this->hasMany(\App\Models\FeatureRequest::class, 'listing_id', 'id'); }

    public function userByHostId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'host_id', 'id'); }

    public function reviewByListingId(): HasMany { return $this->hasMany(\App\Models\Review::class, 'listing_id', 'id'); }
}
