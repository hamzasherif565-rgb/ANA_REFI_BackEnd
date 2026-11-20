<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'booking_id', 'feature_request_id', 'provider', 'status', 'amount_cents', 'currency',
        'intention_id', 'paymob_order_id', 'transaction_id', 'expires_at', 'requires_review',
    ];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'expires_at' => 'datetime', 'requires_review' => 'boolean'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function featureRequest(): BelongsTo
    {
        return $this->belongsTo(FeatureRequest::class);
    }
}
