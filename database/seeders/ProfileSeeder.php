<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        // The database trigger normally creates these rows with each auth user.
        // firstOrCreate also covers installations where that trigger is unavailable.
        foreach (User::query()->limit(5)->get() as $user) {
            $attributes = Profile::factory()->make(['id' => $user->id])->getAttributes();

            Profile::query()->firstOrCreate(['id' => $user->id], $attributes);
        }
    }
}