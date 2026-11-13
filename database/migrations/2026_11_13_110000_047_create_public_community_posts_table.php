<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_community_posts', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->char('listing_id', 36)->nullable();
            $column = $table->longText('title')->nullable();
            $column = $table->longText('body');
            $column = $table->longText('image_url')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'community_posts_pkey');
        });
        Schema::table('public_community_posts', function (Blueprint $table): void {
            $table->foreign('listing_id', 'public_community_posts_listing_id_foreign')->references('id')->on('public_listings')->nullOnDelete();
        });
        Schema::table('public_community_posts', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_community_posts_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_community_posts');
    }
};
