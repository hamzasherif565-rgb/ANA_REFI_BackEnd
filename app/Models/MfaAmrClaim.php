<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MfaAmrClaim extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_mfa_amr_claims';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['session_id', 'created_at', 'updated_at', 'authentication_method', 'id'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function sessionBySessionId(): BelongsTo { return $this->belongsTo(\App\Models\Session::class, 'session_id', 'id'); }
}
