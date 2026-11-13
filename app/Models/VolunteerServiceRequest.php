<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VolunteerServiceRequest extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_volunteer_service_requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'requester_id', 'volunteer_id', 'volunteer_user_id', 'requested_date', 'requested_time', 'location', 'reason', 'people_count', 'notes', 'status', 'created_at', 'updated_at'];
    protected function casts(): array
    {
        return array (
  'requested_date' => 'date',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
);
    }

    public function userByRequesterId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'requester_id', 'id'); }

    public function volunteerRequestByVolunteerId(): BelongsTo { return $this->belongsTo(\App\Models\VolunteerRequest::class, 'volunteer_id', 'id'); }

    public function userByVolunteerUserId(): BelongsTo { return $this->belongsTo(\App\Models\User::class, 'volunteer_user_id', 'id'); }
}
