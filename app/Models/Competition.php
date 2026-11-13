<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Competition extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_competitions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'host_id', 'title', 'title_en', 'description', 'description_en', 'category', 'location', 'starts_at', 'ends_at', 'prize_amount', 'entry_fee', 'capacity', 'participants_count', 'image_url', 'status', 'featured', 'coming_soon', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'starts_at' => 'datetime',
  'ends_at' => 'datetime',
  'featured' => 'boolean',
  'coming_soon' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function userByHostId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'host_id', 'id'); }
}
