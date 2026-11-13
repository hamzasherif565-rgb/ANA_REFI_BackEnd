<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_mfa_challenges', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('factor_id', 36);
            $column = $table->dateTime('created_at');
            $column = $table->dateTime('verified_at')->nullable();
            $column = $table->string('ip_address', 45);
            $column = $table->longText('otp_code')->nullable();
            $column = $table->json('web_authn_session_data')->nullable();
            $table->primary(array (
  0 => 'id',
), 'mfa_challenges_pkey');
        });
        Schema::table('auth_mfa_challenges', function (Blueprint $table): void {
            $table->foreign('factor_id', 'auth_mfa_challenges_factor_id_foreign')->references('id')->on('auth_mfa_factors')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_challenges', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'mfa_challenge_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_challenges');
    }
};
