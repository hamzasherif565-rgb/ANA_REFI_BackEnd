<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ScimUser extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_scim_users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'sso_provider_id', 'user_id', 'resource', 'user_name', 'external_id', 'active', 'created_at', 'updated_at', 'deleted_at'];
    protected function casts(): array
    {
        return array (
  'resource' => 'array',
  'active' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'deleted_at' => 'datetime',
);
    }

    public function ssoProviderBySsoProviderId(): BelongsTo { return $this->belongsTo(\App\Models\SsoProvider::class, 'sso_provider_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
