<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_users', function (Blueprint $table): void {
            $column = $table->char('instance_id', 36)->nullable();
            $column = $table->char('id', 36);
            $column = $table->string('aud', 255)->nullable();
            $column = $table->string('role', 255)->nullable();
            $column = $table->string('email', 255)->nullable();
            $column = $table->string('encrypted_password', 255)->nullable();
            $column = $table->dateTime('email_confirmed_at')->nullable();
            $column = $table->dateTime('invited_at')->nullable();
            $column = $table->string('confirmation_token', 255)->nullable();
            $column = $table->dateTime('confirmation_sent_at')->nullable();
            $column = $table->string('recovery_token', 255)->nullable();
            $column = $table->dateTime('recovery_sent_at')->nullable();
            $column = $table->string('email_change_token_new', 255)->nullable();
            $column = $table->string('email_change', 255)->nullable();
            $column = $table->dateTime('email_change_sent_at')->nullable();
            $column = $table->dateTime('last_sign_in_at')->nullable();
            $column = $table->json('raw_app_meta_data')->nullable();
            $column = $table->json('raw_user_meta_data')->nullable();
            $column = $table->boolean('is_super_admin')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->string('phone', 191)->nullable();
            $column = $table->dateTime('phone_confirmed_at')->nullable();
            $column = $table->string('phone_change', 191)->nullable()->default('');
            $column = $table->string('phone_change_token', 255)->nullable()->default('');
            $column = $table->dateTime('phone_change_sent_at')->nullable();
            $column = $table->dateTime('confirmed_at')->nullable();
            $column = $table->string('email_change_token_current', 255)->nullable()->default('');
            $column = $table->smallInteger('email_change_confirm_status')->nullable()->default('0');
            $column = $table->dateTime('banned_until')->nullable();
            $column = $table->string('reauthentication_token', 255)->nullable()->default('');
            $column = $table->dateTime('reauthentication_sent_at')->nullable();
            $column = $table->boolean('is_sso_user')->default(false);
            $column = $table->dateTime('deleted_at')->nullable();
            $column = $table->boolean('is_anonymous')->default(false);
            $table->primary(array (
  0 => 'id',
), 'users_pkey');
            $table->string('email_non_sso', 191)->nullable()->storedAs("CASE WHEN `is_sso_user` = 0 THEN `email` ELSE NULL END");
            $table->string('confirmation_token_non_numeric', 191)->nullable()->storedAs("CASE WHEN `confirmation_token` REGEXP '^[0-9 ]*$' THEN NULL ELSE `confirmation_token` END");
            $table->string('recovery_token_non_numeric', 191)->nullable()->storedAs("CASE WHEN `recovery_token` REGEXP '^[0-9 ]*$' THEN NULL ELSE `recovery_token` END");
            $table->string('email_change_token_new_non_numeric', 191)->nullable()->storedAs("CASE WHEN `email_change_token_new` REGEXP '^[0-9 ]*$' THEN NULL ELSE `email_change_token_new` END");
            $table->string('reauthentication_token_non_numeric', 191)->nullable()->storedAs("CASE WHEN `reauthentication_token` REGEXP '^[0-9 ]*$' THEN NULL ELSE `reauthentication_token` END");
            $table->string('email_change_token_current_non_numeric', 191)->nullable()->storedAs("CASE WHEN `email_change_token_current` REGEXP '^[0-9 ]*$' THEN NULL ELSE `email_change_token_current` END");
            $table->string('metadata_user_name', 191)->nullable()->storedAs("JSON_UNQUOTE(JSON_EXTRACT(`raw_user_meta_data`, '$.name'))");
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'phone',
), 'users_phone_key');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'email_non_sso',
), 'users_email_partial_key');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'confirmation_token_non_numeric',
), 'confirmation_token_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'instance_id',
), 'users_instance_id_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'instance_id',
), 'users_instance_id_email_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'recovery_token_non_numeric',
), 'recovery_token_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'email_change_token_new_non_numeric',
), 'email_change_token_new_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'reauthentication_token_non_numeric',
), 'reauthentication_token_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'email_change_token_current_non_numeric',
), 'email_change_token_current_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'is_anonymous',
), 'users_is_anonymous_idx');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'email',
), 'idx_users_email');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'idx_users_created_at_desc');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'last_sign_in_at',
), 'idx_users_last_sign_in_at_desc');
        });
        Schema::table('auth_users', function (Blueprint $table): void {
            $table->index(array (
  0 => 'metadata_user_name',
), 'idx_users_name');
        });
        DB::statement("ALTER TABLE `auth_users` ADD CONSTRAINT `users_email_change_confirm_status_check` CHECK (email_change_confirm_status >= 0 AND email_change_confirm_status <= 2)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_users');
    }
};
