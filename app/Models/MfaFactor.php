<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaFactor extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_mfa_factors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'friendly_name', 'factor_type', 'status', 'created_at', 'updated_at', 'secret', 'phone', 'last_challenged_at', 'web_authn_credential', 'web_authn_aaguid', 'last_webauthn_challenge_data'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'last_challenged_at' => 'datetime',
  'web_authn_credential' => 'array',
  'last_webauthn_challenge_data' => 'array',
);
    }

    public function mfaChallengeByFactorId(): HasMany { return $this->hasMany(\App\Models\MfaChallenge::class, 'factor_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }

    public function mfaRecoveryCodeSetByMfaFactorId(): HasOne { return $this->hasOne(\App\Models\MfaRecoveryCodeSet::class, 'mfa_factor_id', 'id'); }
}
