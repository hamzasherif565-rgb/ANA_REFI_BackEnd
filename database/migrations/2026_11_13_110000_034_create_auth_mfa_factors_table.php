<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_mfa_factors', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->string('friendly_name', 191)->nullable();
            $column = $table->enum('factor_type', ['totp', 'webauthn', 'phone', 'recovery_code']);
            $column = $table->enum('status', ['unverified', 'verified']);
            $column = $table->dateTime('created_at');
            $column = $table->dateTime('updated_at');
            $column = $table->longText('secret')->nullable();
            $column = $table->string('phone', 191)->nullable();
            $column = $table->dateTime('last_challenged_at')->nullable();
            $column = $table->json('web_authn_credential')->nullable();
            $column = $table->char('web_authn_aaguid', 36)->nullable();
            $column = $table->json('last_webauthn_challenge_data')->nullable();
            $table->primary(array (
  0 => 'id',
), 'mfa_factors_pkey');
            $table->string('friendly_name_nonblank', 191)->nullable()->storedAs("CASE WHEN TRIM(`friendly_name`) <> '' THEN `friendly_name` ELSE NULL END");
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_mfa_factors_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'mfa_factors_user_id_idx');
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'friendly_name_nonblank',
  1 => 'user_id',
), 'mfa_factors_user_friendly_name_unique');
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'created_at',
), 'factor_id_created_at_idx');
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'last_challenged_at',
), 'mfa_factors_last_challenged_at_key');
        });
        Schema::table('auth_mfa_factors', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'phone',
), 'unique_phone_factor_per_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_factors');
    }
};
