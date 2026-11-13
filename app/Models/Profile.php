<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Profile extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_profiles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'display_name', 'avatar_url', 'bio', 'created_at', 'updated_at', 'account_type', 'is_blocked', 'role_selected', 'age', 'location', 'phone', 'email'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'is_blocked' => 'boolean',
  'role_selected' => 'boolean',
);
    }

    public function userById(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'id', 'id'); }
}
