<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WebauthnCredential extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_webauthn_credentials';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'credential_id', 'public_key', 'attestation_type', 'aaguid', 'sign_count', 'transports', 'backup_eligible', 'backed_up', 'friendly_name', 'created_at', 'updated_at', 'last_used_at'];
    protected function casts(): array
    {
        return array (
  'transports' => 'array',
  'backup_eligible' => 'boolean',
  'backed_up' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'last_used_at' => 'datetime',
);
    }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
