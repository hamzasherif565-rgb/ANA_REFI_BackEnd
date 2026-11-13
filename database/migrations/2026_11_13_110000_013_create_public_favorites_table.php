<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_favorites', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->enum('item_type', ['competition', 'stay', 'experience', 'product']);
            $column = $table->char('item_id', 36);
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'favorites_pkey');
        });
        Schema::table('public_favorites', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_favorites_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_favorites', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'item_type',
  2 => 'item_id',
), 'favorites_user_id_item_type_item_id_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_favorites');
    }
};
