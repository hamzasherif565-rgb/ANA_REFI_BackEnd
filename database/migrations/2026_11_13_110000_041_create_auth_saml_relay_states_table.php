<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_saml_relay_states', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('sso_provider_id', 36);
            $column = $table->longText('request_id');
            $column = $table->string('for_email', 191)->nullable();
            $column = $table->longText('redirect_to')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->char('flow_state_id', 36)->nullable();
            $table->primary(array (
  0 => 'id',
), 'saml_relay_states_pkey');
        });
        Schema::table('auth_saml_relay_states', function (Blueprint $table): void {
            $table->foreign('flow_state_id', 'auth_saml_relay_states_flow_state_id_foreign')->references('id')->on('auth_flow_state')->cascadeOnDelete();
        });
        Schema::table('auth_saml_relay_states', function (Blueprint $table): void {
            $table->foreign('sso_provider_id', 'auth_saml_relay_states_sso_provider_id_foreign')->references('id')->on('auth_sso_providers')->cascadeOnDelete();
        });
        Schema::table('auth_saml_relay_states', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
), 'saml_relay_states_sso_provider_id_idx');
        });
        Schema::table('auth_saml_relay_states', function (Blueprint $table): void {
            $table->index(array (
  0 => 'for_email',
), 'saml_relay_states_for_email_idx');
        });
        Schema::table('auth_saml_relay_states', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'saml_relay_states_created_at_idx');
        });
        DB::statement("ALTER TABLE `auth_saml_relay_states` ADD CONSTRAINT `request_id_not_empty` CHECK (CHAR_LENGTH(request_id) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_saml_relay_states');
    }
};
