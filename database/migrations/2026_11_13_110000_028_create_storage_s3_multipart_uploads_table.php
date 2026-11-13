<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_s3_multipart_uploads', function (Blueprint $table): void {
            $column = $table->string('id', 191);
            $column = $table->bigInteger('in_progress_size')->default('0');
            $column = $table->longText('upload_signature');
            $column = $table->string('bucket_id', 191);
            $column = $table->string('key', 191);
            $column = $table->longText('version');
            $column = $table->longText('owner_id')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->json('user_metadata')->nullable();
            $column = $table->json('metadata')->nullable();
            $table->primary(array (
  0 => 'id',
), 's3_multipart_uploads_pkey');
        });
        Schema::table('storage_s3_multipart_uploads', function (Blueprint $table): void {
            $table->foreign('bucket_id', 'storage_s3_multipart_uploads_bucket_id_foreign')->references('id')->on('storage_buckets');
        });
        Schema::table('storage_s3_multipart_uploads', function (Blueprint $table): void {
            $table->index(array (
  0 => 'bucket_id',
  1 => 'key',
  2 => 'created_at',
), 'idx_multipart_uploads_list');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_s3_multipart_uploads');
    }
};
