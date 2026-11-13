<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_migrations', function (Blueprint $table): void {
            $column = $table->integer('id');
            $column = $table->string('name', 100);
            $column = $table->string('hash', 40);
            $column = $table->dateTime('executed_at')->nullable()->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'migrations_pkey');
        });
        Schema::table('storage_migrations', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'name',
), 'migrations_name_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_migrations');
    }
};
