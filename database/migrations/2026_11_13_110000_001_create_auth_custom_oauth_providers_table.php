<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_custom_oauth_providers', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->enum('provider_type', ['oauth2', 'oidc']);
            $column = $table->string('identifier', 191);
            $column = $table->longText('name');
            $column = $table->longText('client_id');
            $column = $table->longText('client_secret');
            $column = $table->json('acceptable_client_ids')->default(DB::raw('(JSON_ARRAY())'));
            $column = $table->json('scopes')->default(DB::raw('(JSON_ARRAY())'));
            $column = $table->boolean('pkce_enabled')->default(true);
            $column = $table->json('attribute_mapping')->default(DB::raw('(JSON_OBJECT())'));
            $column = $table->json('authorization_params')->default(DB::raw('(JSON_OBJECT())'));
            $column = $table->boolean('enabled')->default(true);
            $column = $table->boolean('email_optional')->default(false);
            $column = $table->longText('issuer')->nullable();
            $column = $table->longText('discovery_url')->nullable();
            $column = $table->boolean('skip_nonce_check')->default(false);
            $column = $table->json('cached_discovery')->nullable();
            $column = $table->dateTime('discovery_cached_at')->nullable();
            $column = $table->longText('authorization_url')->nullable();
            $column = $table->longText('token_url')->nullable();
            $column = $table->longText('userinfo_url')->nullable();
            $column = $table->longText('jwks_uri')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->json('custom_claims_allowlist')->default(DB::raw('(JSON_ARRAY())'));
            $table->primary(array (
  0 => 'id',
), 'custom_oauth_providers_pkey');
        });
        Schema::table('auth_custom_oauth_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'identifier',
), 'custom_oauth_providers_identifier_idx');
        });
        Schema::table('auth_custom_oauth_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'provider_type',
), 'custom_oauth_providers_provider_type_idx');
        });
        Schema::table('auth_custom_oauth_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'enabled',
), 'custom_oauth_providers_enabled_idx');
        });
        Schema::table('auth_custom_oauth_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'custom_oauth_providers_created_at_idx');
        });
        Schema::table('auth_custom_oauth_providers', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'identifier',
), 'custom_oauth_providers_identifier_key');
        });
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_authorization_url_https` CHECK (authorization_url IS NULL OR authorization_url LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_authorization_url_length` CHECK (authorization_url IS NULL OR CHAR_LENGTH(authorization_url) <= 2048)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_client_id_length` CHECK (CHAR_LENGTH(client_id) >= 1 AND CHAR_LENGTH(client_id) <= 512)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_discovery_url_length` CHECK (discovery_url IS NULL OR CHAR_LENGTH(discovery_url) <= 2048)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_identifier_format` CHECK (identifier REGEXP '^[a-z0-9][a-z0-9:-]{0,48}[a-z0-9]$')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_issuer_length` CHECK (issuer IS NULL OR CHAR_LENGTH(issuer) >= 1 AND CHAR_LENGTH(issuer) <= 2048)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_jwks_uri_https` CHECK (jwks_uri IS NULL OR jwks_uri LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_jwks_uri_length` CHECK (jwks_uri IS NULL OR CHAR_LENGTH(jwks_uri) <= 2048)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_name_length` CHECK (CHAR_LENGTH(name) >= 1 AND CHAR_LENGTH(name) <= 100)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_oauth2_requires_endpoints` CHECK (provider_type <> 'oauth2' OR authorization_url IS NOT NULL AND token_url IS NOT NULL AND userinfo_url IS NOT NULL)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_oidc_discovery_url_https` CHECK (provider_type <> 'oidc' OR discovery_url IS NULL OR discovery_url LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_oidc_issuer_https` CHECK (provider_type <> 'oidc' OR issuer IS NULL OR issuer LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_oidc_requires_issuer` CHECK (provider_type <> 'oidc' OR issuer IS NOT NULL)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_provider_type_check` CHECK (provider_type IN ('oauth2', 'oidc'))");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_token_url_https` CHECK (token_url IS NULL OR token_url LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_token_url_length` CHECK (token_url IS NULL OR CHAR_LENGTH(token_url) <= 2048)");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_userinfo_url_https` CHECK (userinfo_url IS NULL OR userinfo_url LIKE 'https://%')");
        DB::statement("ALTER TABLE `auth_custom_oauth_providers` ADD CONSTRAINT `custom_oauth_providers_userinfo_url_length` CHECK (userinfo_url IS NULL OR CHAR_LENGTH(userinfo_url) <= 2048)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_custom_oauth_providers');
    }
};
