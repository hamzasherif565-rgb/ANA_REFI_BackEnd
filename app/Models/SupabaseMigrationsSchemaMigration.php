<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupabaseMigrationsSchemaMigration extends Model
{
    use HasFactory;

    protected $table = 'supabase_migrations_schema_migrations';
    protected $primaryKey = 'version';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['version', 'statements', 'name', 'created_by', 'idempotency_key', 'rollback'];
    protected function casts(): array
    {
        return array (
  'statements' => 'array',
  'rollback' => 'array',
);
    }
}
