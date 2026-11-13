<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_scim_users', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('sso_provider_id', 36);
            $column = $table->char('user_id', 36)->nullable();
            $column = $table->json('resource');
            $column = $table->string('user_name', 191);
            $column = $table->string('external_id', 191)->nullable();
            $column = $table->boolean('active')->default(true);
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->dateTime('deleted_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'scim_users_pkey');
            $table->string('active_user_name', 191)->nullable()->storedAs("CASE WHEN `deleted_at` IS NULL THEN `user_name` ELSE NULL END");
            $table->string('active_external_id', 191)->nullable()->storedAs("CASE WHEN `external_id` IS NOT NULL AND `deleted_at` IS NULL THEN `external_id` ELSE NULL END");
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->foreign('sso_provider_id', 'auth_scim_users_sso_provider_id_foreign')->references('id')->on('auth_sso_providers')->cascadeOnDelete();
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_scim_users_user_id_foreign')->references('id')->on('auth_users')->nullOnDelete();
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'sso_provider_id',
  1 => 'active_user_name',
), 'scim_users_user_name_key');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'sso_provider_id',
  1 => 'active_external_id',
), 'scim_users_external_id_key');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'scim_users_user_id_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
  1 => 'id',
), 'scim_users_id_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
  1 => 'user_name',
  2 => 'id',
), 'scim_users_user_name_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
  1 => 'created_at',
  2 => 'id',
), 'scim_users_created_at_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
  1 => 'updated_at',
  2 => 'id',
), 'scim_users_updated_at_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
), 'scim_users_sso_provider_id_idx');
        });
        Schema::table('auth_scim_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'deleted_at',
), 'scim_users_deleted_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_scim_users');
    }
};
