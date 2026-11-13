<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasUuids;
    use HasFactory, Notifiable;

    protected $table = 'auth_users';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable = ['instance_id', 'id', 'aud', 'role', 'email', 'encrypted_password', 'email_confirmed_at', 'invited_at', 'confirmation_token', 'confirmation_sent_at', 'recovery_token', 'recovery_sent_at', 'email_change_token_new', 'email_change', 'email_change_sent_at', 'last_sign_in_at', 'raw_app_meta_data', 'raw_user_meta_data', 'is_super_admin', 'created_at', 'updated_at', 'phone', 'phone_confirmed_at', 'phone_change', 'phone_change_token', 'phone_change_sent_at', 'confirmed_at', 'email_change_token_current', 'email_change_confirm_status', 'banned_until', 'reauthentication_token', 'reauthentication_sent_at', 'is_sso_user', 'deleted_at', 'is_anonymous'];
    protected $hidden = ['encrypted_password', 'remember_token'];
    public function getAuthPasswordName(): string
    {
        return 'encrypted_password';
    }

    protected function casts(): array
    {
        return array (
  'email_confirmed_at' => 'datetime',
  'invited_at' => 'datetime',
  'confirmation_sent_at' => 'datetime',
  'recovery_sent_at' => 'datetime',
  'email_change_sent_at' => 'datetime',
  'last_sign_in_at' => 'datetime',
  'raw_app_meta_data' => 'array',
  'raw_user_meta_data' => 'array',
  'is_super_admin' => 'boolean',
  'created_at' => 'datetime',
  'updated_at' => 'datetime',
  'phone_confirmed_at' => 'datetime',
  'phone_change_sent_at' => 'datetime',
  'confirmed_at' => 'datetime',
  'banned_until' => 'datetime',
  'reauthentication_sent_at' => 'datetime',
  'is_sso_user' => 'boolean',
  'deleted_at' => 'datetime',
  'is_anonymous' => 'boolean',
);
    }

    public function identityByUserId(): HasMany { return $this->hasMany(\App\Models\Identity::class, 'user_id', 'id'); }

    public function mfaFactorByUserId(): HasMany { return $this->hasMany(\App\Models\MfaFactor::class, 'user_id', 'id'); }

    public function mfaRecoveryCodeSetByUserId(): HasOne { return $this->hasOne(\App\Models\MfaRecoveryCodeSet::class, 'user_id', 'id'); }

    public function oauthAuthorizationByUserId(): HasMany { return $this->hasMany(\App\Models\OauthAuthorization::class, 'user_id', 'id'); }

    public function oauthConsentByUserId(): HasMany { return $this->hasMany(\App\Models\OauthConsent::class, 'user_id', 'id'); }

    public function oneTimeTokenByUserId(): HasMany { return $this->hasMany(\App\Models\OneTimeToken::class, 'user_id', 'id'); }

    public function scimUserByUserId(): HasMany { return $this->hasMany(\App\Models\ScimUser::class, 'user_id', 'id'); }

    public function sessionByUserId(): HasMany { return $this->hasMany(\App\Models\Session::class, 'user_id', 'id'); }

    public function webauthnChallengeByUserId(): HasMany { return $this->hasMany(\App\Models\WebauthnChallenge::class, 'user_id', 'id'); }

    public function webauthnCredentialByUserId(): HasMany { return $this->hasMany(\App\Models\WebauthnCredential::class, 'user_id', 'id'); }

    public function bookingByHostId(): HasMany { return $this->hasMany(\App\Models\Booking::class, 'host_id', 'id'); }

    public function bookingByUserId(): HasMany { return $this->hasMany(\App\Models\Booking::class, 'user_id', 'id'); }

    public function communityPostByUserId(): HasMany { return $this->hasMany(\App\Models\CommunityPost::class, 'user_id', 'id'); }

    public function competitionByHostId(): HasMany { return $this->hasMany(\App\Models\Competition::class, 'host_id', 'id'); }

    public function favoriteByUserId(): HasMany { return $this->hasMany(\App\Models\Favorite::class, 'user_id', 'id'); }

    public function featureRequestByHostId(): HasMany { return $this->hasMany(\App\Models\FeatureRequest::class, 'host_id', 'id'); }

    public function hostVerificationByUserId(): HasOne { return $this->hasOne(\App\Models\HostVerification::class, 'user_id', 'id'); }

    public function listingByHostId(): HasMany { return $this->hasMany(\App\Models\Listing::class, 'host_id', 'id'); }

    public function notificationByUserId(): HasMany { return $this->hasMany(\App\Models\Notification::class, 'user_id', 'id'); }

    public function profileById(): HasMany { return $this->hasMany(\App\Models\Profile::class, 'id', 'id'); }

    public function reviewByUserId(): HasMany { return $this->hasMany(\App\Models\Review::class, 'user_id', 'id'); }

    public function userContactByUserId(): HasMany { return $this->hasMany(\App\Models\UserContact::class, 'user_id', 'id'); }

    public function userRoleByUserId(): HasMany { return $this->hasMany(\App\Models\UserRole::class, 'user_id', 'id'); }

    public function volunteerServiceRequestByRequesterId(): HasMany { return $this->hasMany(\App\Models\VolunteerServiceRequest::class, 'requester_id', 'id'); }

    public function volunteerServiceRequestByVolunteerUserId(): HasMany { return $this->hasMany(\App\Models\VolunteerServiceRequest::class, 'volunteer_user_id', 'id'); }
}
