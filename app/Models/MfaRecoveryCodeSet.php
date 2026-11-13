<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaRecoveryCodeSet extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_mfa_recovery_code_sets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'mfa_factor_id', 'failed_verification_count', 'verification_locked_until', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'verification_locked_until' => 'datetime',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function mfaFactorByMfaFactorId(): BelongsTo { return $this->belongsTo(\App\Models\MfaFactor::class, 'mfa_factor_id', 'id'); }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }

    public function mfaRecoveryCodeByMfaRecoveryCodeSetId(): HasMany { return $this->hasMany(\App\Models\MfaRecoveryCode::class, 'mfa_recovery_code_set_id', 'id'); }
}
