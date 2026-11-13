<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ScimToken extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_scim_tokens';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'sso_provider_id', 'token_hash', 'prefix', 'created_at', 'expires_at', 'revoked_at', 'last_used_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'expires_at' => 'datetime',
  'revoked_at' => 'datetime',
  'last_used_at' => 'datetime',
);
    }

    public function ssoProviderBySsoProviderId(): BelongsTo { return $this->belongsTo(\App\Models\SsoProvider::class, 'sso_provider_id', 'id'); }
}
