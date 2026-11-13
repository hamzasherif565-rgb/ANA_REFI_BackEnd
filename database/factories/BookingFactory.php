<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'item_type' => 'stay',
            'item_id' => fake()->uuid(),
            'host_id' => null,
            'status' => fake()->randomElement(array (
  0 => 'pending',
  1 => 'confirmed',
  2 => 'cancelled',
  3 => 'completed',
)),
            'amount' => 0,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'listing_id' => null,
            'booking_date' => null,
            'guests' => 1,
        ];
    }
}
