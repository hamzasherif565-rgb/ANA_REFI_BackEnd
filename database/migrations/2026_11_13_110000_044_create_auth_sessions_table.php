<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_sessions', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->char('factor_id', 36)->nullable();
            $column = $table->enum('aal', ['aal1', 'aal2', 'aal3'])->nullable();
            $column = $table->dateTime('not_after')->nullable();
            $column = $table->dateTime('refreshed_at')->nullable();
            $column = $table->longText('user_agent')->nullable();
            $column = $table->string('ip', 45)->nullable();
            $column = $table->longText('tag')->nullable();
            $column = $table->char('oauth_client_id', 36)->nullable();
            $column = $table->longText('refresh_token_hmac_key')->nullable();
            $column = $table->bigInteger('refresh_token_counter')->nullable();
            $column = $table->longText('scopes')->nullable();
            $table->primary(array (
  0 => 'id',
), 'sessions_pkey');
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->foreign('oauth_client_id', 'auth_sessions_oauth_client_id_foreign')->references('id')->on('auth_oauth_clients')->cascadeOnDelete();
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_sessions_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'not_after',
), 'sessions_not_after_idx');
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'created_at',
), 'user_id_created_at_idx');
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'sessions_user_id_idx');
        });
        Schema::table('auth_sessions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'oauth_client_id',
), 'sessions_oauth_client_id_idx');
        });
        DB::statement("ALTER TABLE `auth_sessions` ADD CONSTRAINT `sessions_scopes_length` CHECK (CHAR_LENGTH(scopes) <= 4096)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_sessions');
    }
};
