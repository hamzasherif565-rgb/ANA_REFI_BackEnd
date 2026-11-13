<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaChallenge extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_mfa_challenges';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'factor_id', 'created_at', 'verified_at', 'ip_address', 'otp_code', 'web_authn_session_data'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'verified_at' => 'datetime',
  'web_authn_session_data' => 'array',
);
    }

    public function mfaFactorByFactorId(): BelongsTo { return $this->belongsTo(\App\Models\MfaFactor::class, 'factor_id', 'id'); }
}
