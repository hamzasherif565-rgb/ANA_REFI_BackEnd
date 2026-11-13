<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OauthConsent extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_oauth_consents';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'client_id', 'scopes', 'granted_at', 'revoked_at'];
    protected function casts(): array
    {
        return array (
  'granted_at' => 'datetime',
  'revoked_at' => 'datetime',
);
    }

    public function oauthClientByClientId(): BelongsTo { return $this->belongsTo(\App\Models\OauthClient::class, 'client_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
