<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault_secrets', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->string('name', 191)->nullable();
            $column = $table->string('description', 191)->default('');
            $column = $table->longText('secret');
            $column = $table->char('key_id', 36)->nullable();
            $column = $table->binary('nonce')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'secrets_pkey');
        });
        DB::statement('ALTER TABLE `vault_secrets` MODIFY `nonce` LONGBLOB NULL');
        Schema::table('vault_secrets', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'name',
), 'secrets_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_secrets');
    }
};
