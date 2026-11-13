<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SsoProvider extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_sso_providers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'resource_id', 'created_at', 'updated_at', 'disabled'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'disabled' => 'boolean',
);
    }

    public function samlProviderBySsoProviderId(): HasMany { return $this->hasMany(\App\Models\SamlProvider::class, 'sso_provider_id', 'id'); }

    public function samlRelayStateBySsoProviderId(): HasMany { return $this->hasMany(\App\Models\SamlRelayState::class, 'sso_provider_id', 'id'); }

    public function scimTokenBySsoProviderId(): HasMany { return $this->hasMany(\App\Models\ScimToken::class, 'sso_provider_id', 'id'); }

    public function scimUserBySsoProviderId(): HasMany { return $this->hasMany(\App\Models\ScimUser::class, 'sso_provider_id', 'id'); }

    public function ssoDomainBySsoProviderId(): HasMany { return $this->hasMany(\App\Models\SsoDomain::class, 'sso_provider_id', 'id'); }
}
