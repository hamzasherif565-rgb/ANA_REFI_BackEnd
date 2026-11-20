<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserRole extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_user_roles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'role', 'created_at'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
);
    }

    public static function forAccountType(string $accountType): string
    {
        return $accountType === 'traveler' ? 'user' : $accountType;
    }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
