<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OauthAuthorization extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_oauth_authorizations';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'authorization_id', 'client_id', 'user_id', 'redirect_uri', 'scope', 'state', 'resource', 'code_challenge', 'code_challenge_method', 'response_type', 'status', 'authorization_code', 'created_at', 'expires_at', 'approved_at', 'nonce'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'expires_at' => 'datetime',
  'approved_at' => 'datetime',
);
    }

    public function oauthClientByClientId(): BelongsTo { return $this->belongsTo(\App\Models\OauthClient::class, 'client_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
