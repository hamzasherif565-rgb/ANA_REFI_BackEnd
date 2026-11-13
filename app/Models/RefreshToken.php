<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = 'auth_refresh_tokens';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = ['instance_id', 'id', 'token', 'user_id', 'revoked', 'created_at', 'updated_at', 'parent', 'session_id'];
    protected function casts(): array
    {
        return array (
  'revoked' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function sessionBySessionId(): BelongsTo { return $this->belongsTo(\App\Models\Session::class, 'session_id', 'id'); }
}
