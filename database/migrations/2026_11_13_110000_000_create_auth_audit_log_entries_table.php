<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_audit_log_entries', function (Blueprint $table): void {
            $column = $table->char('instance_id', 36)->nullable();
            $column = $table->char('id', 36);
            $column = $table->json('payload')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->string('ip_address', 64)->default('');
            $table->primary(array (
  0 => 'id',
), 'audit_log_entries_pkey');
        });
        Schema::table('auth_audit_log_entries', function (Blueprint $table): void {
            $table->index(array (
  0 => 'instance_id',
), 'audit_logs_instance_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_audit_log_entries');
    }
};
