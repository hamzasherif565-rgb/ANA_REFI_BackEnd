<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('storage_buckets')) {
            Schema::create('storage_buckets', function (Blueprint $table): void {
                $column = $table->string('id', 191);
                $column = $table->string('name', 191);
                $column = $table->char('owner', 36)->nullable();
                $column = $table->dateTime('created_at')->nullable()->useCurrent();
                $column = $table->dateTime('updated_at')->nullable()->useCurrent();
                $column = $table->boolean('public')->nullable()->default(false);
                $column = $table->boolean('avif_autodetection')->nullable()->default(false);
                $column = $table->bigInteger('file_size_limit')->nullable();
                $column = $table->json('allowed_mime_types')->nullable();
                $column = $table->longText('owner_id')->nullable();
                $column = $table->enum('type', ['STANDARD', 'ANALYTICS', 'VECTOR'])->default('STANDARD');
                $column = $table->enum('versioning_status', ['DISABLED', 'ENABLED', 'SUSPENDED'])->default('DISABLED');
                $column = $table->json('lifecycle_configuration')->nullable();
                $column = $table->char('lifecycle_configuration_generation', 36)->nullable();
                $table->primary(array (
  0 => 'id',
                ), 'buckets_pkey');
            });
        }

        if (! $this->hasConstraint('bname')) {
            Schema::table('storage_buckets', function (Blueprint $table): void {
                $table->unique(array (
  0 => 'name',
                ), 'bname');
            });
        }

        $checks = [
            'buckets_lifecycle_configuration_pair_check' => "(lifecycle_configuration IS NULL) = (lifecycle_configuration_generation IS NULL)",
            'buckets_lifecycle_configuration_shape_check' => "lifecycle_configuration IS NULL OR JSON_TYPE(lifecycle_configuration) = 'OBJECT' AND JSON_CONTAINS_PATH(lifecycle_configuration, 'one', '$.rules') AND CASE WHEN JSON_TYPE(JSON_EXTRACT(lifecycle_configuration, '$.rules')) = 'ARRAY' THEN JSON_LENGTH(JSON_EXTRACT(lifecycle_configuration, '$.rules')) >= 1 AND JSON_LENGTH(JSON_EXTRACT(lifecycle_configuration, '$.rules')) <= 1000 ELSE false END",
            'buckets_lifecycle_configuration_standard_only_check' => "type = 'STANDARD' OR lifecycle_configuration IS NULL AND lifecycle_configuration_generation IS NULL",
            'buckets_versioning_dark_check' => "versioning_status = 'DISABLED'",
            'buckets_versioning_standard_only_check' => "type = 'STANDARD' OR versioning_status = 'DISABLED'",
            'buckets_versioning_status_check' => "versioning_status IN ('DISABLED', 'ENABLED', 'SUSPENDED')",
        ];

        foreach ($checks as $name => $expression) {
            if (! $this->hasConstraint($name)) {
                DB::statement("ALTER TABLE `storage_buckets` ADD CONSTRAINT `{$name}` CHECK ({$expression})");
            }
        }
    }

    private function hasConstraint(string $name): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::connection()->getDatabaseName())
            ->where('table_name', 'storage_buckets')
            ->where('constraint_name', $name)
            ->exists();
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_buckets');
    }
};
