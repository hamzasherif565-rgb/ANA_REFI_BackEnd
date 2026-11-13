<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('storage_s3_multipart_uploads_parts')) {
            Schema::create('storage_s3_multipart_uploads_parts', function (Blueprint $table): void {
                $column = $table->char('id', 36);
                $column = $table->string('upload_id', 191);
                $column = $table->bigInteger('size')->default('0');
                $column = $table->integer('part_number');
                $column = $table->string('bucket_id', 191);
                $column = $table->longText('key');
                $column = $table->longText('etag');
                $column = $table->longText('owner_id')->nullable();
                $column = $table->longText('version');
                $column = $table->dateTime('created_at')->useCurrent();
                $table->primary(array (
  0 => 'id',
                ), 's3_multipart_uploads_parts_pkey');
            });
        } else {
            // A prior failed MySQL DDL attempt left the table with TEXT columns.
            // These identifiers mirror VARCHAR(191) parent keys and must match
            // exactly for InnoDB foreign keys. Refuse narrowing if data exists.
            $hasOversizedIdentifiers = DB::table('storage_s3_multipart_uploads_parts')
                ->whereRaw('CHAR_LENGTH(`upload_id`) > 191 OR CHAR_LENGTH(`bucket_id`) > 191')
                ->exists();

            if ($hasOversizedIdentifiers) {
                throw new RuntimeException('Cannot convert existing multipart identifiers longer than 191 characters without changing data.');
            }

            DB::statement('ALTER TABLE `storage_s3_multipart_uploads_parts` MODIFY `upload_id` VARCHAR(191) NOT NULL, MODIFY `bucket_id` VARCHAR(191) NOT NULL');
        }

        if (! $this->hasConstraint('storage_s3_multipart_uploads_parts_bucket_id_foreign')) {
            Schema::table('storage_s3_multipart_uploads_parts', function (Blueprint $table): void {
                $table->foreign('bucket_id', 'storage_s3_multipart_uploads_parts_bucket_id_foreign')->references('id')->on('storage_buckets');
            });
        }

        if (! $this->hasConstraint('storage_s3_multipart_uploads_parts_upload_id_foreign')) {
            Schema::table('storage_s3_multipart_uploads_parts', function (Blueprint $table): void {
                $table->foreign('upload_id', 'storage_s3_multipart_uploads_parts_upload_id_foreign')->references('id')->on('storage_s3_multipart_uploads')->cascadeOnDelete();
            });
        }
    }

    private function hasConstraint(string $name): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::connection()->getDatabaseName())
            ->where('table_name', 'storage_s3_multipart_uploads_parts')
            ->where('constraint_name', $name)
            ->exists();
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_s3_multipart_uploads_parts');
    }
};
