<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SamlRelayState extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_saml_relay_states';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'sso_provider_id', 'request_id', 'for_email', 'redirect_to', 'created_at', 'updated_at', 'flow_state_id'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function flowStateByFlowStateId(): BelongsTo { return $this->belongsTo(\App\Models\FlowState::class, 'flow_state_id', 'id'); }

    public function ssoProviderBySsoProviderId(): BelongsTo { return $this->belongsTo(\App\Models\SsoProvider::class, 'sso_provider_id', 'id'); }
}
