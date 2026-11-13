<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Session extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_sessions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'created_at', 'updated_at', 'factor_id', 'aal', 'not_after', 'refreshed_at', 'user_agent', 'ip', 'tag', 'oauth_client_id', 'refresh_token_hmac_key', 'refresh_token_counter', 'scopes'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'not_after' => 'datetime',
  'refreshed_at' => 'datetime',
);
    }

    public function mfaAmrClaimBySessionId(): HasMany { return $this->hasMany(\App\Models\MfaAmrClaim::class, 'session_id', 'id'); }

    public function refreshTokenBySessionId(): HasMany { return $this->hasMany(\App\Models\RefreshToken::class, 'session_id', 'id'); }

    public function oauthClientByOauthClientId(): BelongsTo { return $this->belongsTo(\App\Models\OauthClient::class, 'oauth_client_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
