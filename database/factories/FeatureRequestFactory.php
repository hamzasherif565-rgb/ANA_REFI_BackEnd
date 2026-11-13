<?php

namespace Database\Factories;

use App\Models\FeatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FeatureRequest> */
class FeatureRequestFactory extends Factory
{
    protected $model = FeatureRequest::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'listing_id' => \App\Models\Listing::factory(),
            'host_id' => \App\Models\User::factory(),
            'amount' => 500,
            'payment_method' => 'visa',
            'payment_status' => 'paid',
            'status' => 'pending',
            'admin_note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
