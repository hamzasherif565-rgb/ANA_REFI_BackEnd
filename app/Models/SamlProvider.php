<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SamlProvider extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_saml_providers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'sso_provider_id', 'entity_id', 'metadata_xml', 'metadata_url', 'attribute_mapping', 'created_at', 'updated_at', 'name_id_format'];
    protected function casts(): array
    {
        return array (
  'attribute_mapping' => 'array',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function ssoProviderBySsoProviderId(): BelongsTo { return $this->belongsTo(\App\Models\SsoProvider::class, 'sso_provider_id', 'id'); }
}
