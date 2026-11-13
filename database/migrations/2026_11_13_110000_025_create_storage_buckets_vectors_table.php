<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_buckets_vectors', function (Blueprint $table): void {
            $column = $table->string('id', 191);
            $column = $table->enum('type', ['STANDARD', 'ANALYTICS', 'VECTOR'])->default('VECTOR');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'buckets_vectors_pkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_buckets_vectors');
    }
};
