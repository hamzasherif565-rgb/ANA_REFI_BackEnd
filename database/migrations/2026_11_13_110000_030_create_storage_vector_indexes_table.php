<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_vector_indexes', function (Blueprint $table): void {
            $column = $table->string('id', 191);
            $column = $table->string('name', 191);
            $column = $table->string('bucket_id', 191);
            $column = $table->longText('data_type');
            $column = $table->integer('dimension');
            $column = $table->longText('distance_metric');
            $column = $table->json('metadata_configuration')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'vector_indexes_pkey');
        });
        Schema::table('storage_vector_indexes', function (Blueprint $table): void {
            $table->foreign('bucket_id', 'storage_vector_indexes_bucket_id_foreign')->references('id')->on('storage_buckets_vectors');
        });
        Schema::table('storage_vector_indexes', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'name',
  1 => 'bucket_id',
), 'vector_indexes_name_bucket_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_vector_indexes');
    }
};
