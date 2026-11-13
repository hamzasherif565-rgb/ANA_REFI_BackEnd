<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_instances', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('uuid', 36)->nullable();
            $column = $table->longText('raw_base_config')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'instances_pkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_instances');
    }
};
