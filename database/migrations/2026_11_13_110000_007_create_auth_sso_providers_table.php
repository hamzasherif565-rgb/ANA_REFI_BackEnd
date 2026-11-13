<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_sso_providers', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->string('resource_id', 191)->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->boolean('disabled')->nullable();
            $table->primary(array (
  0 => 'id',
), 'sso_providers_pkey');
        });
        Schema::table('auth_sso_providers', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'resource_id',
), 'sso_providers_resource_id_idx');
        });
        Schema::table('auth_sso_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'resource_id',
), 'sso_providers_resource_id_pattern_idx');
        });
        DB::statement("ALTER TABLE `auth_sso_providers` ADD CONSTRAINT `resource_id_not_empty` CHECK (resource_id = NULL OR CHAR_LENGTH(resource_id) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_sso_providers');
    }
};
