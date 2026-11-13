<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'instance_id' => null,
            'id' => fake()->uuid(),
            'aud' => null,
            'role' => null,
            'email' => fake()->unique()->safeEmail(),
            'encrypted_password' => Hash::make('password'),
            'email_confirmed_at' => null,
            'invited_at' => null,
            'confirmation_token' => null,
            'confirmation_sent_at' => null,
            'recovery_token' => null,
            'recovery_sent_at' => null,
            'email_change_token_new' => null,
            'email_change' => null,
            'email_change_sent_at' => null,
            'last_sign_in_at' => null,
            'raw_app_meta_data' => null,
            'raw_user_meta_data' => null,
            'is_super_admin' => null,
            'created_at' => null,
            'updated_at' => null,
            'phone' => null,
            'phone_confirmed_at' => null,
            'phone_change' => null,
            'phone_change_token' => null,
            'phone_change_sent_at' => null,
            'confirmed_at' => null,
            'email_change_token_current' => null,
            'email_change_confirm_status' => null,
            'banned_until' => null,
            'reauthentication_token' => null,
            'reauthentication_sent_at' => null,
            'is_sso_user' => false,
            'deleted_at' => null,
            'is_anonymous' => false,
        ];
    }
}
