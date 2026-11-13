<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_identities', function (Blueprint $table): void {
            $column = $table->string('provider_id', 191);
            $column = $table->char('user_id', 36);
            $column = $table->json('identity_data');
            $column = $table->string('provider', 191);
            $column = $table->dateTime('last_sign_in_at')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->string('email', 191)->nullable();
            $column = $table->char('id', 36);
            $table->primary(array (
  0 => 'id',
), 'identities_pkey');
        });
        Schema::table('auth_identities', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_identities_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_identities', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'identities_user_id_idx');
        });
        Schema::table('auth_identities', function (Blueprint $table): void {
            $table->index(array (
  0 => 'email',
), 'identities_email_idx');
        });
        Schema::table('auth_identities', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'provider_id',
  1 => 'provider',
), 'identities_provider_id_provider_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_identities');
    }
};
