<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_objects', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->string('bucket_id', 191)->nullable();
            $column = $table->string('name', 191)->nullable();
            $column = $table->char('owner', 36)->nullable();
            $column = $table->dateTime('created_at')->nullable()->useCurrent();
            $column = $table->dateTime('updated_at')->nullable()->useCurrent();
            $column = $table->dateTime('last_accessed_at')->nullable()->useCurrent();
            $column = $table->json('metadata')->nullable();
            $column = $table->json('path_tokens')->nullable();
            $column = $table->string('version', 191)->nullable();
            $column = $table->longText('owner_id')->nullable();
            $column = $table->json('user_metadata')->nullable();
            $column = $table->dateTime('archived_at')->nullable();
            $column = $table->boolean('is_delete_marker')->default(false);
            $column = $table->boolean('is_versioned')->default(false);
            $table->primary(array (
  0 => 'id',
), 'objects_pkey');
            $table->string('current_object_name', 191)->nullable()->storedAs("CASE WHEN `archived_at` IS NULL THEN `name` ELSE NULL END");
            $table->string('unversioned_object_name', 191)->nullable()->storedAs("CASE WHEN `is_versioned` = 0 THEN `name` ELSE NULL END");
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->foreign('bucket_id', 'storage_objects_bucket_id_foreign')->references('id')->on('storage_buckets');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->index(array (
  0 => 'name',
), 'name_prefix_search');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->index(array (
  0 => 'bucket_id',
  1 => 'name',
), 'idx_objects_bucket_id_name');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->index(array (
  0 => 'bucket_id',
), 'idx_objects_bucket_id_name_lower');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'bucket_id',
  1 => 'name',
  2 => 'version',
), 'objects_bucket_id_name_version_key');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'bucket_id',
  1 => 'current_object_name',
), 'idx_objects_current_version');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'bucket_id',
  1 => 'unversioned_object_name',
), 'idx_objects_null_version');
        });
        Schema::table('storage_objects', function (Blueprint $table): void {
            $table->index(array (
  0 => 'bucket_id',
  1 => 'name',
), 'idx_objects_delete_markers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_objects');
    }
};
