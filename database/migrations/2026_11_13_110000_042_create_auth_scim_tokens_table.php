<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_scim_tokens', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('sso_provider_id', 36);
            $column = $table->string('token_hash', 191);
            $column = $table->longText('prefix');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('expires_at')->nullable();
            $column = $table->dateTime('revoked_at')->nullable();
            $column = $table->dateTime('last_used_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'scim_tokens_pkey');
        });
        Schema::table('auth_scim_tokens', function (Blueprint $table): void {
            $table->foreign('sso_provider_id', 'auth_scim_tokens_sso_provider_id_foreign')->references('id')->on('auth_sso_providers')->cascadeOnDelete();
        });
        Schema::table('auth_scim_tokens', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'token_hash',
), 'scim_tokens_token_hash_key');
        });
        Schema::table('auth_scim_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
), 'scim_tokens_sso_provider_id_idx');
        });
        Schema::table('auth_scim_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'expires_at',
), 'scim_tokens_expires_at_idx');
        });
        Schema::table('auth_scim_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'revoked_at',
), 'scim_tokens_revoked_at_idx');
        });
        DB::statement("ALTER TABLE `auth_scim_tokens` ADD CONSTRAINT `scim_tokens_expires_at_future` CHECK (expires_at IS NULL OR expires_at > created_at)");
        DB::statement("ALTER TABLE `auth_scim_tokens` ADD CONSTRAINT `scim_tokens_revoked_after_created` CHECK (revoked_at IS NULL OR revoked_at >= created_at)");
        DB::statement("ALTER TABLE `auth_scim_tokens` ADD CONSTRAINT `scim_tokens_token_hash_check` CHECK (token_hash REGEXP '^[0-9a-f]{64}$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_scim_tokens');
    }
};
