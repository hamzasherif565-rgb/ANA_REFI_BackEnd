<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Review extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_reviews';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'listing_id', 'user_id', 'rating', 'comment', 'image_url', 'created_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
);
    }

    public function listingByListingId(): BelongsTo { return $this->belongsTo(\App\Models\Listing::class, 'listing_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
