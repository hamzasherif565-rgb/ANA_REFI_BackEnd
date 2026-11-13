<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuditLogEntry extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'auth_audit_log_entries';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['instance_id', 'id', 'payload', 'created_at', 'ip_address'];
    protected function casts(): array
    {
        return array (
  'payload' => 'array',
  'created_at' => 'datetime',
);
    }
}
