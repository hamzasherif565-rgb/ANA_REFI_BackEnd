<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_buckets_analytics', function (Blueprint $table): void {
            $column = $table->string('name', 191);
            $column = $table->enum('type', ['STANDARD', 'ANALYTICS', 'VECTOR'])->default('ANALYTICS');
            $column = $table->string('format', 191)->default('ICEBERG');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->char('id', 36);
            $column = $table->dateTime('deleted_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'buckets_analytics_pkey');
            $table->string('active_bucket_name', 191)->nullable()->storedAs("CASE WHEN `deleted_at` IS NULL THEN `name` ELSE NULL END");
        });
        Schema::table('storage_buckets_analytics', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'active_bucket_name',
), 'buckets_analytics_unique_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_buckets_analytics');
    }
};
