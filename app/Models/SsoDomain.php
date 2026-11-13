<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SsoDomain extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_sso_domains';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'sso_provider_id', 'domain', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function ssoProviderBySsoProviderId(): BelongsTo { return $this->belongsTo(\App\Models\SsoProvider::class, 'sso_provider_id', 'id'); }
}
