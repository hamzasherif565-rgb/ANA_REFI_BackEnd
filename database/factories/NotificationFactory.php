<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Notification> */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => \App\Models\User::factory(),
            'type' => fake()->randomElement(array (
  0 => 'booking_confirmed',
  1 => 'booking_status',
  2 => 'competition_update',
  3 => 'new_message',
  4 => 'system',
)),
            'title' => fake()->sentence(),
            'body' => null,
            'link' => null,
            'read' => false,
            'created_at' => now(),
        ];
    }
}
