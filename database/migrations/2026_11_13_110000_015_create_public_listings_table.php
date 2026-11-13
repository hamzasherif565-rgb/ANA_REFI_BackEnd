<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_listings', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('host_id', 36);
            $column = $table->enum('type', ['stay', 'experience', 'product', 'competition']);
            $column = $table->longText('title');
            $column = $table->longText('title_en')->nullable();
            $column = $table->longText('description')->nullable();
            $column = $table->longText('description_en')->nullable();
            $column = $table->longText('category')->nullable();
            $column = $table->integer('price')->default('0');
            $column = $table->integer('discount')->default('0');
            $column = $table->integer('capacity')->nullable();
            $column = $table->longText('location')->nullable();
            $column = $table->json('images')->default(DB::raw('(JSON_ARRAY())'));
            $column = $table->dateTime('starts_at')->nullable();
            $column = $table->dateTime('ends_at')->nullable();
            $column = $table->boolean('is_featured')->default(false);
            $column = $table->enum('status', ['active', 'blocked', 'draft'])->default('active');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'listings_pkey');
        });
        Schema::table('public_listings', function (Blueprint $table): void {
            $table->foreign('host_id', 'public_listings_host_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        DB::statement("ALTER TABLE `public_listings` ADD CONSTRAINT `listings_status_check` CHECK (status IN ('active', 'blocked', 'draft'))");
        DB::statement("ALTER TABLE `public_listings` ADD CONSTRAINT `listings_type_check` CHECK (type IN ('stay', 'experience', 'product', 'competition'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('public_listings');
    }
};
