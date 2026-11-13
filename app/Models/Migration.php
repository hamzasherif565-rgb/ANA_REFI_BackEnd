<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Migration extends Model
{
    use HasFactory;

    protected $table = 'storage_migrations';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'hash', 'executed_at'];
    protected function casts(): array
    {
        return array (
  'executed_at' => 'datetime',
);
    }
}
