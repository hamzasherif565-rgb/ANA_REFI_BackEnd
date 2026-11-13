<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_mfa_amr_claims', function (Blueprint $table): void {
            $column = $table->char('session_id', 36);
            $column = $table->dateTime('created_at');
            $column = $table->dateTime('updated_at');
            $column = $table->string('authentication_method', 191);
            $column = $table->char('id', 36);
            $table->primary(array (
  0 => 'id',
), 'mfa_amr_claims_pkey');
        });
        Schema::table('auth_mfa_amr_claims', function (Blueprint $table): void {
            $table->foreign('session_id', 'auth_mfa_amr_claims_session_id_foreign')->references('id')->on('auth_sessions')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_amr_claims', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'session_id',
  1 => 'authentication_method',
), 'mfa_amr_claims_session_id_authentication_method_pkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_amr_claims');
    }
};
