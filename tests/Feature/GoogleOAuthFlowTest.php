<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Tests\TestCase;

class GoogleOAuthFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google.frontend_url' => 'http://localhost:3000']);
        config(['cors.allowed_origins' => ['http://localhost:3000']]);
        config(['app.url' => 'https://api.example.test']);

        Schema::create('auth_users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('email')->unique();
            $table->string('encrypted_password');
            $table->string('aud')->nullable();
            $table->string('role')->nullable();
            $table->string('phone')->nullable();
            $table->json('raw_user_meta_data')->nullable();
            $table->boolean('is_sso_user')->default(false);
            $table->timestamp('email_confirmed_at')->nullable();
            $table->timestamp('banned_until')->nullable();
            $table->timestamp('last_sign_in_at')->nullable();
            $table->timestamps();
        });
        Schema::create('public_profiles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('display_name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('email')->nullable();
            $table->string('account_type')->nullable();
            $table->boolean('role_selected')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();
        });
        Schema::create('public_user_roles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('user_id');
            $table->string('role');
        });
        Schema::create('public_user_contacts', function (Blueprint $table): void {
            $table->string('user_id')->primary();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('public_user_contacts');
        Schema::dropIfExists('public_user_roles');
        Schema::dropIfExists('public_profiles');
        Schema::dropIfExists('auth_users');
        parent::tearDown();
    }

    public function test_google_redirect_gracefully_returns_to_frontend_when_credentials_are_missing(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $this->get('/api/v1/auth/google/redirect?redirect_uri='.urlencode('http://localhost:3000/auth'))
            ->assertRedirect('http://localhost:3000/auth?oauth_error=google_not_configured');
    }

    public function test_google_redirect_rejects_unapproved_frontend_origins(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);

        $this->get('/api/v1/auth/google/redirect?redirect_uri='.urlencode('https://attacker.example/auth'))
            ->assertUnprocessable();
    }

    public function test_google_callback_handles_user_cancellation(): void
    {
        Cache::put('google_oauth_state:test-state', 'http://localhost:3000/auth', now()->addMinutes(10));

        $this->get('/api/v1/auth/google/callback?state=test-state&error=access_denied')
            ->assertRedirect('http://localhost:3000/auth?oauth_error=cancelled');
    }

    public function test_google_callback_reports_expired_or_invalid_state_to_the_frontend(): void
    {
        $this->get('/api/v1/auth/google/callback?state=expired&code=unused')
            ->assertRedirect('http://localhost:3000/auth?oauth_error=callback_failed');
    }

    public function test_google_redirect_starts_authorization_code_flow_when_configured(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);

        $response = $this->get('/api/v1/auth/google/redirect?redirect_uri='.urlencode('http://localhost:3000/auth'));
        $response->assertRedirect();
        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/v2/auth?', $response->headers->get('Location'));
        parse_str((string) parse_url((string) $response->headers->get('Location'), PHP_URL_QUERY), $query);
        $this->assertSame('code', $query['response_type'] ?? null);
        $this->assertSame('test-client-id', $query['client_id'] ?? null);
        $this->assertNotEmpty($query['state'] ?? null);
        $this->assertSame('http://localhost:3000/auth', Cache::get('google_oauth_state:'.$query['state']));
    }

    public function test_google_callback_creates_a_new_user_and_returns_a_laravel_token(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);
        Cache::put('google_oauth_state:new-user', 'http://localhost:3000/auth', now()->addMinutes(10));
        $this->fakeGoogleAccount('new@example.test');

        $response = $this->get('/api/v1/auth/google/callback?state=new-user&code=google-code');

        $response->assertRedirect();
        $this->assertStringStartsWith('http://localhost:3000/auth#api_token=', $response->headers->get('Location'));
        $this->assertDatabaseHas('auth_users', ['email' => 'new@example.test', 'is_sso_user' => true]);
        $this->assertDatabaseHas('public_profiles', ['email' => 'new@example.test', 'display_name' => 'New Google User']);
        $this->assertDatabaseHas('public_user_roles', ['role' => 'user']);
    }

    public function test_google_callback_uses_an_existing_account_instead_of_creating_a_duplicate(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);
        $user = User::create([
            'id' => 'existing-account', 'email' => 'existing@example.test', 'encrypted_password' => 'existing-hash',
            'aud' => 'authenticated', 'role' => 'authenticated',
        ]);
        $user->userRoleByUserId()->create(['role' => 'host']);
        Cache::put('google_oauth_state:existing-user', 'http://localhost:3000/auth', now()->addMinutes(10));
        $this->fakeGoogleAccount('existing@example.test');

        $response = $this->get('/api/v1/auth/google/callback?state=existing-user&code=google-code');

        $response->assertRedirect();
        $this->assertSame(1, User::where('email', 'existing@example.test')->count());
        $this->assertDatabaseHas('public_user_roles', ['user_id' => $user->id, 'role' => 'host']);
    }

    public function test_google_and_password_login_reject_accounts_blocked_in_the_profile(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);
        $user = User::create([
            'id' => 'blocked-account', 'email' => 'blocked@example.test', 'encrypted_password' => bcrypt('StrongPass123!'),
            'aud' => 'authenticated', 'role' => 'authenticated',
        ]);
        \App\Models\Profile::create(['id' => $user->id, 'email' => $user->email, 'is_blocked' => true]);

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'StrongPass123!'])
            ->assertForbidden();

        Cache::put('google_oauth_state:blocked-user', 'http://localhost:3000/auth', now()->addMinutes(10));
        $this->fakeGoogleAccount($user->email);
        $this->get('/api/v1/auth/google/callback?state=blocked-user&code=google-code')
            ->assertRedirect('http://localhost:3000/auth?oauth_error=account_blocked');
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_email_registration_login_current_user_and_logout_work_with_sanctum_tokens(): void
    {
        $registration = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Test', 'last_name' => 'Traveler', 'email' => 'traveler@example.test',
            'phone' => '01000000000', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
            'account_type' => 'traveler',
        ])->assertCreated();
        $this->assertNotEmpty($registration->json('token'));

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'traveler@example.test', 'password' => 'StrongPass123!',
        ])->assertOk();
        $token = $login->json('token');
        $this->assertNotEmpty($token);

        $this->withToken($token)->getJson('/api/v1/auth/user')
            ->assertOk()->assertJsonPath('data.user.email', 'traveler@example.test')
            ->assertJsonPath('data.profile.account_type', 'traveler');
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();
        app('auth')->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/auth/user')->assertUnauthorized();
    }

    public function test_expired_sanctum_tokens_are_rejected(): void
    {
        $registration = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Test', 'last_name' => 'Traveler', 'email' => 'expiry@example.test',
            'phone' => '01000000001', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
            'account_type' => 'traveler',
        ])->assertCreated();
        config(['sanctum.expiration' => 1]);

        $this->travel(2)->minutes();
        $this->withToken($registration->json('token'))->getJson('/api/v1/auth/user')->assertUnauthorized();
        $this->travelBack();
    }

    public function test_google_callback_redirects_back_when_google_rejects_the_token_exchange(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);
        Cache::put('google_oauth_state:failed-exchange', 'http://localhost:3000/auth', now()->addMinutes(10));
        Http::fake(['oauth2.googleapis.com/token' => Http::response(['error' => 'invalid_grant'], 400)]);

        $this->get('/api/v1/auth/google/callback?state=failed-exchange&code=invalid-code')
            ->assertRedirect('http://localhost:3000/auth?oauth_error=callback_failed');
    }

    private function fakeGoogleAccount(string $email): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake-google-access-token'], 200),
            'www.googleapis.com/oauth2/v2/userinfo' => Http::response([
                'email' => $email, 'verified_email' => true, 'name' => 'New Google User',
                'picture' => 'https://images.example.test/profile.png',
            ], 200),
        ]);
    }
}
