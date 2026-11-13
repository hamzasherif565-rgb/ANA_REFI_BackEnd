<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_reviews', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('listing_id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->integer('rating');
            $column = $table->longText('comment')->nullable();
            $column = $table->longText('image_url')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'reviews_pkey');
        });
        Schema::table('public_reviews', function (Blueprint $table): void {
            $table->foreign('listing_id', 'public_reviews_listing_id_foreign')->references('id')->on('public_listings')->cascadeOnDelete();
        });
        Schema::table('public_reviews', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_reviews_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        DB::statement("ALTER TABLE `public_reviews` ADD CONSTRAINT `reviews_rating_check` CHECK (rating >= 1 AND rating <= 5)");
    }

    public function down(): void
    {
        Schema::dropIfExists('public_reviews');
    }
};
