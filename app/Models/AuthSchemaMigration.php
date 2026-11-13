<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthSchemaMigration extends Model
{
    use HasFactory;

    protected $table = 'auth_schema_migrations';
    protected $primaryKey = 'version';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['version'];
}
