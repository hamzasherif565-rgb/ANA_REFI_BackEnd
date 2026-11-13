<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_bookings', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->enum('item_type', ['competition', 'stay', 'experience', 'product']);
            $column = $table->char('item_id', 36);
            $column = $table->char('host_id', 36)->nullable();
            $column = $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $column = $table->integer('amount')->default('0');
            $column = $table->longText('notes')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->char('listing_id', 36)->nullable();
            $column = $table->date('booking_date')->nullable();
            $column = $table->integer('guests')->default('1');
            $table->primary(array (
  0 => 'id',
), 'bookings_pkey');
            $table->string('active_competition_item_id', 191)->nullable()->storedAs("CASE WHEN `item_type` = 'competition' AND `status` <> 'cancelled' THEN `item_id` ELSE NULL END");
        });
        Schema::table('public_bookings', function (Blueprint $table): void {
            $table->foreign('host_id', 'public_bookings_host_id_foreign')->references('id')->on('auth_users')->nullOnDelete();
        });
        Schema::table('public_bookings', function (Blueprint $table): void {
            $table->foreign('listing_id', 'public_bookings_listing_id_foreign')->references('id')->on('public_listings')->cascadeOnDelete();
        });
        Schema::table('public_bookings', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_bookings_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_bookings', function (Blueprint $table): void {
            $table->index(array (
  0 => 'host_id',
  1 => 'booking_date',
), 'bookings_host_date_idx');
        });
        Schema::table('public_bookings', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'active_competition_item_id',
), 'bookings_unique_active_competition_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_bookings');
    }
};
