<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_oauth_authorizations', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->string('authorization_id', 191);
            $column = $table->char('client_id', 36);
            $column = $table->char('user_id', 36)->nullable();
            $column = $table->longText('redirect_uri');
            $column = $table->longText('scope');
            $column = $table->longText('state')->nullable();
            $column = $table->longText('resource')->nullable();
            $column = $table->longText('code_challenge')->nullable();
            $column = $table->enum('code_challenge_method', ['s256', 'plain'])->nullable();
            $column = $table->enum('response_type', ['code'])->default('code');
            $column = $table->enum('status', ['pending', 'approved', 'denied', 'expired'])->default('pending');
            $column = $table->string('authorization_code', 191)->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('expires_at')->default(DB::raw('(CURRENT_TIMESTAMP + INTERVAL 3 MINUTE)'));
            $column = $table->dateTime('approved_at')->nullable();
            $column = $table->longText('nonce')->nullable();
            $table->primary(array (
  0 => 'id',
), 'oauth_authorizations_pkey');
        });
        Schema::table('auth_oauth_authorizations', function (Blueprint $table): void {
            $table->foreign('client_id', 'auth_oauth_authorizations_client_id_foreign')->references('id')->on('auth_oauth_clients')->cascadeOnDelete();
        });
        Schema::table('auth_oauth_authorizations', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_oauth_authorizations_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_oauth_authorizations', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'authorization_id',
), 'oauth_authorizations_authorization_id_key');
        });
        Schema::table('auth_oauth_authorizations', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'authorization_code',
), 'oauth_authorizations_authorization_code_key');
        });
        Schema::table('auth_oauth_authorizations', function (Blueprint $table): void {
            $table->index(array (
  0 => 'expires_at',
), 'oauth_auth_pending_exp_idx');
        });
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_authorization_code_length` CHECK (CHAR_LENGTH(authorization_code) <= 255)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_code_challenge_length` CHECK (CHAR_LENGTH(code_challenge) <= 128)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_expires_at_future` CHECK (expires_at > created_at)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_nonce_length` CHECK (CHAR_LENGTH(nonce) <= 255)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_redirect_uri_length` CHECK (CHAR_LENGTH(redirect_uri) <= 2048)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_resource_length` CHECK (CHAR_LENGTH(resource) <= 2048)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_scope_length` CHECK (CHAR_LENGTH(scope) <= 4096)");
        DB::statement("ALTER TABLE `auth_oauth_authorizations` ADD CONSTRAINT `oauth_authorizations_state_length` CHECK (CHAR_LENGTH(state) <= 4096)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_authorizations');
    }
};
