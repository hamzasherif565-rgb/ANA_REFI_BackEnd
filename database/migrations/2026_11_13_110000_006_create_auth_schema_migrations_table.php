<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_schema_migrations', function (Blueprint $table): void {
            $column = $table->string('version', 255);
            $table->primary(array (
  0 => 'version',
), 'schema_migrations_pkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_schema_migrations');
    }
};
