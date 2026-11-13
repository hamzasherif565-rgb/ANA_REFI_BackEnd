<?php

namespace Database\Factories;

use App\Models\VolunteerServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VolunteerServiceRequest> */
class VolunteerServiceRequestFactory extends Factory
{
    protected $model = VolunteerServiceRequest::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'requester_id' => \App\Models\User::factory(),
            'volunteer_id' => \App\Models\VolunteerRequest::factory(),
            'volunteer_user_id' => \App\Models\User::factory(),
            'requested_date' => fake()->date(),
            'requested_time' => null,
            'location' => fake()->sentence(),
            'reason' => fake()->sentence(),
            'people_count' => 1,
            'notes' => null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
