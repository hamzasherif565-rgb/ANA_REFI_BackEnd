<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_bookings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'item_type', 'item_id', 'host_id', 'status', 'amount', 'notes', 'created_at', 'updated_at', 'listing_id', 'booking_date', 'guests'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'booking_date' => 'date',
);
    }

    public function userByHostId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'host_id', 'id'); }

    public function listingByListingId(): BelongsTo { return $this->belongsTo(\App\Models\Listing::class, 'listing_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
