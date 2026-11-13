<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_mfa_recovery_code_sets', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->char('mfa_factor_id', 36);
            $column = $table->integer('failed_verification_count')->default('0');
            $column = $table->dateTime('verification_locked_until')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'mfa_recovery_code_sets_pkey');
        });
        Schema::table('auth_mfa_recovery_code_sets', function (Blueprint $table): void {
            $table->foreign('mfa_factor_id', 'auth_mfa_recovery_code_sets_mfa_factor_id_foreign')->references('id')->on('auth_mfa_factors')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_recovery_code_sets', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_mfa_recovery_code_sets_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_mfa_recovery_code_sets', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
), 'mfa_recovery_code_sets_user_id_key');
        });
        Schema::table('auth_mfa_recovery_code_sets', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'mfa_factor_id',
), 'mfa_recovery_code_sets_mfa_factor_id_key');
        });
        DB::statement("ALTER TABLE `auth_mfa_recovery_code_sets` ADD CONSTRAINT `mfa_recovery_code_sets_failed_verification_count_check` CHECK (failed_verification_count >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_mfa_recovery_code_sets');
    }
};
