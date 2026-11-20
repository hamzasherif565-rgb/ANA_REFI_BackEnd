<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class VolunteersEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('auth_users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->boolean('is_super_admin')->default(false);
            $table->timestamps();
        });
        Schema::create('public_user_roles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('role');
        });
        Schema::create('public_volunteer_requests', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id')->nullable();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('interest_area');
            $table->string('availability');
            $table->string('motivation');
            $table->string('status');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('public_volunteer_requests');
        Schema::dropIfExists('public_user_roles');
        Schema::dropIfExists('auth_users');
        parent::tearDown();
    }

    public function test_guest_only_sees_approved_volunteers(): void
    {
        $this->createVolunteer('approved');
        $this->createVolunteer('pending');

        $this->getJson('/api/v1/volunteers')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.status', 'approved');
    }

    public function test_authenticated_non_admin_only_sees_approved_volunteers(): void
    {
        $this->createVolunteer('approved');
        $this->createVolunteer('pending');

        $this->actingAs($this->createUser())
            ->getJson('/api/v1/volunteers')
            ->assertOk()
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.status', 'approved');
    }

    public function test_admin_sees_all_volunteer_applications(): void
    {
        $this->createVolunteer('approved');
        $this->createVolunteer('pending');
        $admin = $this->createUser();
        \Illuminate\Support\Facades\DB::table('public_user_roles')->insert([
            'id' => (string) Str::uuid(), 'user_id' => $admin->id, 'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/v1/volunteers')
            ->assertOk()
            ->assertJsonCount(2, 'data.data');
    }

    public function test_authenticated_user_can_retrieve_own_pending_application(): void
    {
        $user = $this->createUser();
        $this->createVolunteer('pending', $user->id);

        $this->actingAs($user)
            ->getJson('/api/v1/volunteers/application')
            ->assertOk()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.user_id', $user->id);
    }

    private function createUser(): User
    {
        $user = new User();
        $user->forceFill(['id' => (string) Str::uuid(), 'is_super_admin' => false])->save();
        return $user;
    }

    private function createVolunteer(string $status, ?string $userId = null): void
    {
        \Illuminate\Support\Facades\DB::table('public_volunteer_requests')->insert([
            'id' => (string) Str::uuid(), 'user_id' => $userId ?? (string) Str::uuid(), 'full_name' => 'Test Volunteer',
            'email' => 'volunteer@example.test', 'phone' => '01234567890', 'interest_area' => 'Farming',
            'availability' => 'Weekends', 'motivation' => 'I want to help local communities.', 'status' => $status,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
