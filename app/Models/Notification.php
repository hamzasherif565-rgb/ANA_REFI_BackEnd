<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_notifications';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['id', 'user_id', 'type', 'title', 'body', 'link', 'read', 'created_at'];
    protected function casts(): array
    {
        return array (
  'read' => 'boolean',
  'created_at' => 'datetime',
);
    }

    public function userByUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'user_id', 'id'); }
}
