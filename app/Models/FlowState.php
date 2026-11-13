<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FlowState extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_flow_state';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'auth_code', 'code_challenge_method', 'code_challenge', 'provider_type', 'provider_access_token', 'provider_refresh_token', 'created_at', 'updated_at', 'authentication_method', 'auth_code_issued_at', 'invite_token', 'referrer', 'oauth_client_state_id', 'linking_target_id', 'email_optional'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'auth_code_issued_at' => 'datetime',
  'email_optional' => 'boolean',
);
    }

    public function samlRelayStateByFlowStateId(): HasMany { return $this->hasMany(\App\Models\SamlRelayState::class, 'flow_state_id', 'id'); }
}
