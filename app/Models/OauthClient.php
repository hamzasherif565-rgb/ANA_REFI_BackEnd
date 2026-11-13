<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OauthClient extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_oauth_clients';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'client_secret_hash', 'registration_type', 'redirect_uris', 'grant_types', 'client_name', 'client_uri', 'logo_uri', 'created_at', 'updated_at', 'deleted_at', 'client_type', 'token_endpoint_auth_method'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'deleted_at' => 'datetime',
);
    }

    public function oauthAuthorizationByClientId(): HasMany { return $this->hasMany(\App\Models\OauthAuthorization::class, 'client_id', 'id'); }

    public function oauthConsentByClientId(): HasMany { return $this->hasMany(\App\Models\OauthConsent::class, 'client_id', 'id'); }

    public function sessionByOauthClientId(): HasMany { return $this->hasMany(\App\Models\Session::class, 'oauth_client_id', 'id'); }
}
