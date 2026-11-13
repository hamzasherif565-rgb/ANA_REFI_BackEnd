<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_webauthn_credentials', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            // WebAuthn credential IDs are binary and capped at 1023 bytes by the
            // protocol, so VARBINARY keeps the source unique index enforceable.
            $column = $table->binary('credential_id', 1024);
            $column = $table->binary('public_key');
            $column = $table->string('attestation_type', 191)->default('');
            $column = $table->char('aaguid', 36)->nullable();
            $column = $table->bigInteger('sign_count')->default('0');
            $column = $table->json('transports');
            $column = $table->boolean('backup_eligible')->default(false);
            $column = $table->boolean('backed_up')->default(false);
            $column = $table->string('friendly_name', 191)->default('');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->dateTime('last_used_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'webauthn_credentials_pkey');
        });
        DB::statement('ALTER TABLE `auth_webauthn_credentials` MODIFY `public_key` LONGBLOB NOT NULL');
        Schema::table('auth_webauthn_credentials', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_webauthn_credentials_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_webauthn_credentials', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'webauthn_credentials_user_id_idx');
        });
        Schema::table('auth_webauthn_credentials', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'credential_id',
), 'webauthn_credentials_credential_id_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_webauthn_credentials');
    }
};
