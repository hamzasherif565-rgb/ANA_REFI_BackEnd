<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VolunteerRequest extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'public_volunteer_requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['id', 'user_id', 'full_name', 'email', 'phone', 'interest_area', 'availability', 'motivation', 'status', 'created_at', 'updated_at', 'age', 'avatar_url', 'bio', 'location', 'skills', 'languages', 'experience', 'accessibility', 'rating'];
    protected function casts(): array
    {
        return array (
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'skills' => 'array',
  'languages' => 'array',
  'rating' => 'decimal:1',
);
    }

    public function volunteerServiceRequestByVolunteerId(): HasMany { return $this->hasMany(\App\Models\VolunteerServiceRequest::class, 'volunteer_id', 'id'); }
}
