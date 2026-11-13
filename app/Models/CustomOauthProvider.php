<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CustomOauthProvider extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_custom_oauth_providers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'provider_type', 'identifier', 'name', 'client_id', 'client_secret', 'acceptable_client_ids', 'scopes', 'pkce_enabled', 'attribute_mapping', 'authorization_params', 'enabled', 'email_optional', 'issuer', 'discovery_url', 'skip_nonce_check', 'cached_discovery', 'discovery_cached_at', 'authorization_url', 'token_url', 'userinfo_url', 'jwks_uri', 'created_at', 'updated_at', 'custom_claims_allowlist'];
    protected function casts(): array
    {
        return array (
  'acceptable_client_ids' => 'array',
  'scopes' => 'array',
  'pkce_enabled' => 'boolean',
  'attribute_mapping' => 'array',
  'authorization_params' => 'array',
  'enabled' => 'boolean',
  'email_optional' => 'boolean',
  'skip_nonce_check' => 'boolean',
  'cached_discovery' => 'array',
  'discovery_cached_at' => 'datetime',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'custom_claims_allowlist' => 'array',
);
    }
}
