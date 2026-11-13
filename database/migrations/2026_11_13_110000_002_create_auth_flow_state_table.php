<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_flow_state', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36)->nullable();
            $column = $table->string('auth_code', 191)->nullable();
            $column = $table->enum('code_challenge_method', ['s256', 'plain'])->nullable();
            $column = $table->longText('code_challenge')->nullable();
            $column = $table->longText('provider_type');
            $column = $table->longText('provider_access_token')->nullable();
            $column = $table->longText('provider_refresh_token')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->string('authentication_method', 191);
            $column = $table->dateTime('auth_code_issued_at')->nullable();
            $column = $table->longText('invite_token')->nullable();
            $column = $table->longText('referrer')->nullable();
            $column = $table->char('oauth_client_state_id', 36)->nullable();
            $column = $table->char('linking_target_id', 36)->nullable();
            $column = $table->boolean('email_optional')->default(false);
            $table->primary(array (
  0 => 'id',
), 'flow_state_pkey');
        });
        Schema::table('auth_flow_state', function (Blueprint $table): void {
            $table->index(array (
  0 => 'auth_code',
), 'idx_auth_code');
        });
        Schema::table('auth_flow_state', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'authentication_method',
), 'idx_user_id_auth_method');
        });
        Schema::table('auth_flow_state', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'flow_state_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_flow_state');
    }
};
