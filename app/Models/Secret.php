<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Secret extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'vault_secrets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'name', 'description', 'secret', 'key_id', 'nonce', 'created_at', 'updated_at'];

    protected static function booted(): void
    {
        static::creating(function (Secret $secret): void {
            if ($secret->nonce === null) {
                $secret->nonce = random_bytes(24);
            }
        });
    }

    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }
}
