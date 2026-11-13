<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_mfa_recovery_codes', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('mfa_recovery_code_set_id', 36);
            $column = $table->longText('code_hash');
            $column = $table->dateTime('consumed_at')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'mfa_recovery_codes_pkey');
        });
        Schema::table('auth_mfa_recovery_codes', function (Blueprint $table): void {
            $table->foreign('mfa_recovery_code_set_id', 'auth_mfa_recovery_codes_mfa_recovery_code_set_id_foreign')->references('id')->on('auth_mfa_recovery_code_sets')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_recovery_codes', function (Blueprint $table): void {
            $table->index(array (
  0 => 'mfa_recovery_code_set_id',
), 'mfa_recovery_codes_set_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_recovery_codes');
    }
};
