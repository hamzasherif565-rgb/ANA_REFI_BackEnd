<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AuditLogEntrySeeder::class,
            CustomOauthProviderSeeder::class,
            FlowStateSeeder::class,
            InstanceSeeder::class,
            OauthClientStateSeeder::class,
            OauthClientSeeder::class,
            AuthSchemaMigrationSeeder::class,
            SsoProviderSeeder::class,
            UserSeeder::class,
            WebauthnChallengeSeeder::class,
            WebauthnCredentialSeeder::class,
            CompetitionSeeder::class,
            ContactMessageSeeder::class,
            FavoriteSeeder::class,
            HostVerificationSeeder::class,
            ListingSeeder::class,
            NotificationSeeder::class,
            ProfileSeeder::class,
            ReviewSeeder::class,
            UserContactSeeder::class,
            UserRoleSeeder::class,
            VolunteerRequestSeeder::class,
            VolunteerServiceRequestSeeder::class,
            BucketSeeder::class,
            BucketsAnalyticSeeder::class,
            BucketsVectorSeeder::class,
            MigrationSeeder::class,
            StorageObjectSeeder::class,
            S3MultipartUploadSeeder::class,
            S3MultipartUploadsPartSeeder::class,
            VectorIndexSeeder::class,
            SupabaseMigrationsSchemaMigrationSeeder::class,
            SecretSeeder::class,
            IdentitySeeder::class,
            MfaFactorSeeder::class,
            MfaRecoveryCodeSetSeeder::class,
            MfaRecoveryCodeSeeder::class,
            OauthAuthorizationSeeder::class,
            OauthConsentSeeder::class,
            OneTimeTokenSeeder::class,
            SamlProviderSeeder::class,
            SamlRelayStateSeeder::class,
            ScimTokenSeeder::class,
            ScimUserSeeder::class,
            SessionSeeder::class,
            SsoDomainSeeder::class,
            BookingSeeder::class,
            CommunityPostSeeder::class,
            FeatureRequestSeeder::class,
            MfaAmrClaimSeeder::class,
            MfaChallengeSeeder::class,
            RefreshTokenSeeder::class,
        ]);
    }
}
