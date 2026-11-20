<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HostVerification;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserContact;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function googleRedirect(Request $request)
    {
        $clientId = config('services.google.client_id');
        $frontUrl = rtrim(config('services.google.frontend_url'), '/');
        $return = (string) $request->query('redirect_uri', $frontUrl.'/auth');
        $returnOrigin = $this->originOf($return);
        $allowedOrigins = array_filter(array_merge([$this->originOf($frontUrl)], config('cors.allowed_origins', [])));
        abort_unless($returnOrigin && in_array($returnOrigin, $allowedOrigins, true), 422, 'Invalid OAuth return URL.');
        if (!$clientId || !config('services.google.client_secret')) {
            return redirect()->away($this->oauthErrorUrl($return, 'google_not_configured'))
                ->withHeaders(['Referrer-Policy' => 'no-referrer']);
        }
        $state = Str::random(48);
        Cache::put('google_oauth_state:'.$state, $return, now()->addMinutes(10));
        $redirectUri = route('api.v1.auth.google.callback');
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $clientId, 'redirect_uri' => $redirectUri, 'response_type' => 'code',
            'scope' => 'openid email profile', 'state' => $state, 'prompt' => 'select_account',
        ]);
        return redirect()->away($url);
    }

    public function googleCallback(Request $request)
    {
        $return = Cache::pull('google_oauth_state:'.(string) $request->query('state'));
        $fallbackReturn = rtrim(config('services.google.frontend_url'), '/').'/auth';
        if (!$return) {
            return redirect()->away($this->oauthErrorUrl($fallbackReturn, 'callback_failed'));
        }
        if ($request->query('error') === 'access_denied') {
            return redirect()->away($this->oauthErrorUrl($return, 'cancelled'))
                ->withHeaders(['Referrer-Policy' => 'no-referrer']);
        }
        if (!$request->filled('code')) {
            return redirect()->away($this->oauthErrorUrl($return, 'callback_failed'));
        }

        try {
            $tokenResponse = Http::asForm()->connectTimeout(5)->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'code' => $request->query('code'), 'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'), 'redirect_uri' => route('api.v1.auth.google.callback'),
                'grant_type' => 'authorization_code',
            ])->throw()->json();
            $googleUser = Http::withToken($tokenResponse['access_token'])->connectTimeout(5)->timeout(10)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo')->throw()->json();
        } catch (\Throwable $error) {
            Log::warning('Google OAuth callback failed', ['exception' => $error::class]);
            return redirect()->away($this->oauthErrorUrl($return, 'callback_failed'))
                ->withHeaders(['Referrer-Policy' => 'no-referrer']);
        }
        if (empty($googleUser['email']) || !($googleUser['verified_email'] ?? false)) {
            return redirect()->away($this->oauthErrorUrl($return, 'unverified_email'));
        }

        $user = User::where('email', $googleUser['email'])->first();
        if (!$user) {
            $name = preg_split('/\s+/', trim((string) ($googleUser['name'] ?? '')));
            $user = User::create(['id' => (string) Str::uuid(), 'email' => $googleUser['email'], 'encrypted_password' => Hash::make(Str::random(64)),
                'aud' => 'authenticated', 'role' => 'authenticated', 'email_confirmed_at' => now(), 'is_sso_user' => true,
                'raw_user_meta_data' => ['name' => $googleUser['name'] ?? '', 'picture' => $googleUser['picture'] ?? null]]);
            Profile::updateOrCreate(['id' => $user->id], ['display_name' => $googleUser['name'] ?? null, 'avatar_url' => $googleUser['picture'] ?? null, 'email' => $user->email]);
            // The legacy role table stores `user` while profile.account_type stores `traveler`.
            $user->userRoleByUserId()->firstOrCreate(['role' => 'user']);
        }
        if ($this->accountIsBlocked($user)) {
            return redirect()->away($this->oauthErrorUrl($return, 'account_blocked'));
        }
        $user->tokens()->delete();
        $token = $user->createToken('react-app')->plainTextToken;
        return redirect()->away(rtrim($return, '/').'#api_token='.rawurlencode($token))->withHeaders(['Referrer-Policy' => 'no-referrer']);
    }

    private function originOf(string $url): ?string
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return null;
        }
        return strtolower($parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : ''));
    }

    private function oauthErrorUrl(string $return, string $error): string
    {
        $parts = parse_url($return) ?: [];
        parse_str($parts['query'] ?? '', $query);
        $query['oauth_error'] = $error;
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? 'localhost').(isset($parts['port']) ? ':'.$parts['port'] : '');
        return $origin.($parts['path'] ?? '/auth').'?'.http_build_query($query).(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:auth_users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:auth_users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'in:traveler,host'],
            'national_id' => ['required_if:account_type,host', 'nullable', 'digits:14'],
            'id_front' => ['sometimes', 'nullable', 'string', 'max:1024'],
            'id_back' => ['sometimes', 'nullable', 'string', 'max:1024'],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $user = User::create([
                'id' => (string) str()->uuid(), 'email' => $data['email'], 'phone' => $data['phone'],
                'encrypted_password' => Hash::make($data['password']), 'aud' => 'authenticated', 'role' => 'authenticated',
                'raw_user_meta_data' => ['first_name' => $data['first_name'], 'last_name' => $data['last_name']],
            ]);

            $profile = Profile::firstOrNew(['id' => $user->id]);
            $profile->fill(['display_name' => trim($data['first_name'].' '.$data['last_name']), 'email' => $user->email,
                'account_type' => $data['account_type'], 'role_selected' => true]);
            $profile->save();
            UserContact::updateOrCreate(['user_id' => $user->id], ['phone' => $data['phone']]);
            $user->userRoleByUserId()->firstOrCreate(['role' => UserRole::forAccountType($data['account_type'])]);

            if ($data['account_type'] === 'host' && !empty($data['id_front']) && !empty($data['id_back'])) {
                HostVerification::updateOrCreate(['user_id' => $user->id], [
                    'first_name' => $data['first_name'], 'last_name' => $data['last_name'],
                    'national_id' => $data['national_id'], 'id_front_path' => $data['id_front'],
                    'id_back_path' => $data['id_back'], 'status' => 'pending',
                ]);
            }

            return $user;
        });

        return response()->json($this->payload($user), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], (string) $user->encrypted_password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 422);
        }

        if ($this->accountIsBlocked($user)) {
            return response()->json(['message' => 'This account is blocked.'], 403);
        }

        $user->forceFill(['last_sign_in_at' => now()])->save();
        return response()->json($this->payload($user));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->noContent();
    }

    public function user(Request $request)
    {
        return response()->json(['data' => $this->userData($request->user(), true)]);
    }

    private function payload(User $user): array
    {
        $user->tokens()->delete();
        $token = $user->createToken('react-app')->plainTextToken;
        return ['data' => $this->userData($user, true), 'token' => $token, 'token_type' => 'Bearer'];
    }

    private function userData(User $user, bool $includePhone): array
    {
        $profile = Profile::firstOrCreate(['id' => $user->id]);
        $roles = $user->userRoleByUserId()->pluck('role')->values();
        $phone = $includePhone ? UserContact::where('user_id', $user->id)->value('phone') : null;
        return ['user' => ['id' => $user->id, 'email' => $user->email], 'profile' => [
            'id' => $profile->id, 'display_name' => $profile->display_name, 'avatar_url' => $profile->avatar_url,
            'bio' => $profile->bio, 'account_type' => $profile->account_type, 'role_selected' => $profile->role_selected,
            'is_blocked' => $profile->is_blocked, 'age' => $profile->age, 'location' => $profile->location,
            'email' => $user->email, 'phone' => $phone,
        ], 'roles' => $roles];
    }

    private function accountIsBlocked(User $user): bool
    {
        return ($user->banned_until && $user->banned_until->isFuture())
            || (bool) Profile::whereKey($user->id)->value('is_blocked');
    }
}
