<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_oauth_consents', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->char('client_id', 36);
            $column = $table->longText('scopes');
            $column = $table->dateTime('granted_at')->useCurrent();
            $column = $table->dateTime('revoked_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'oauth_consents_pkey');
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->foreign('client_id', 'auth_oauth_consents_client_id_foreign')->references('id')->on('auth_oauth_clients')->cascadeOnDelete();
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_oauth_consents_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'client_id',
), 'oauth_consents_user_client_unique');
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'client_id',
), 'oauth_consents_active_user_client_idx');
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'granted_at',
), 'oauth_consents_user_order_idx');
        });
        Schema::table('auth_oauth_consents', function (Blueprint $table): void {
            $table->index(array (
  0 => 'client_id',
), 'oauth_consents_active_client_idx');
        });
        DB::statement("ALTER TABLE `auth_oauth_consents` ADD CONSTRAINT `oauth_consents_revoked_after_granted` CHECK (revoked_at IS NULL OR revoked_at >= granted_at)");
        DB::statement("ALTER TABLE `auth_oauth_consents` ADD CONSTRAINT `oauth_consents_scopes_length` CHECK (CHAR_LENGTH(scopes) <= 2048)");
        DB::statement("ALTER TABLE `auth_oauth_consents` ADD CONSTRAINT `oauth_consents_scopes_not_empty` CHECK (CHAR_LENGTH(TRIM(scopes)) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_consents');
    }
};
