<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_refresh_tokens', function (Blueprint $table): void {
            $column = $table->char('instance_id', 36)->nullable();
            $column = $table->bigInteger('id')->autoIncrement();
            $column = $table->string('token', 255)->nullable();
            $column = $table->string('user_id', 255)->nullable();
            $column = $table->boolean('revoked')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->string('parent', 255)->nullable();
            $column = $table->char('session_id', 36)->nullable();
            $table->primary(array (
  0 => 'id',
), 'refresh_tokens_pkey');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->foreign('session_id', 'auth_refresh_tokens_session_id_foreign')->references('id')->on('auth_sessions')->cascadeOnDelete();
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'updated_at',
), 'refresh_tokens_updated_at_idx');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'instance_id',
), 'refresh_tokens_instance_id_idx');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'instance_id',
  1 => 'user_id',
), 'refresh_tokens_instance_id_user_id_idx');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'token',
), 'refresh_tokens_token_unique');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'parent',
), 'refresh_tokens_parent_idx');
        });
        Schema::table('auth_refresh_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'session_id',
  1 => 'revoked',
), 'refresh_tokens_session_id_revoked_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_refresh_tokens');
    }
};
