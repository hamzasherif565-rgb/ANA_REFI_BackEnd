<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Identity extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_identities';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['provider_id', 'user_id', 'identity_data', 'provider', 'last_sign_in_at', 'created_at', 'updated_at', 'email', 'id'];
    protected function casts(): array
    {
        return array (
  'identity_data' => 'array',
  'last_sign_in_at' => 'datetime',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
