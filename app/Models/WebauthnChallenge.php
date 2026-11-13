<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebauthnChallenge extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_webauthn_challenges';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'challenge_type', 'session_data', 'created_at', 'expires_at'];
    protected function casts(): array
    {
        return array (
  'session_data' => 'array',
  'created_at' => 'datetime',
  'expires_at' => 'datetime',
);
    }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
