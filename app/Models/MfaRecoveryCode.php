<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaRecoveryCode extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_mfa_recovery_codes';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'mfa_recovery_code_set_id', 'code_hash', 'consumed_at', 'created_at'];
    protected function casts(): array
    {
        return array (
  'consumed_at' => 'datetime',
  'created_at' => 'datetime',
);
    }

    public function mfaRecoveryCodeSetByMfaRecoveryCodeSetId(): BelongsTo { return $this->belongsTo(\App\Models\MfaRecoveryCodeSet::class, 'mfa_recovery_code_set_id', 'id'); }
}
