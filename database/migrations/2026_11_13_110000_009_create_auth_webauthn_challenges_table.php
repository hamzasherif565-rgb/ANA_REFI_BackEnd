<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_webauthn_challenges', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36)->nullable();
            $column = $table->enum('challenge_type', ['signup', 'registration', 'authentication']);
            $column = $table->json('session_data');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('expires_at');
            $table->primary(array (
  0 => 'id',
), 'webauthn_challenges_pkey');
        });
        Schema::table('auth_webauthn_challenges', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_webauthn_challenges_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_webauthn_challenges', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
), 'webauthn_challenges_user_id_idx');
        });
        Schema::table('auth_webauthn_challenges', function (Blueprint $table): void {
            $table->index(array (
  0 => 'expires_at',
), 'webauthn_challenges_expires_at_idx');
        });
        DB::statement("ALTER TABLE `auth_webauthn_challenges` ADD CONSTRAINT `webauthn_challenges_challenge_type_check` CHECK (challenge_type IN ('signup', 'registration', 'authentication'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_webauthn_challenges');
    }
};
