<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supabase_migrations_schema_migrations', function (Blueprint $table): void {
            $column = $table->string('version', 191);
            $column = $table->json('statements')->nullable();
            $column = $table->longText('name')->nullable();
            $column = $table->longText('created_by')->nullable();
            $column = $table->string('idempotency_key', 191)->nullable();
            $column = $table->json('rollback')->nullable();
            $table->primary(array (
  0 => 'version',
), 'schema_migrations_pkey');
        });
        Schema::table('supabase_migrations_schema_migrations', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'idempotency_key',
), 'schema_migrations_idempotency_key_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supabase_migrations_schema_migrations');
    }
};
