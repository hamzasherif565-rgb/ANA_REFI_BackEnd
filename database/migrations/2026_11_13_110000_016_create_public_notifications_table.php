<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_notifications', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->enum('type', ['booking_confirmed', 'booking_status', 'competition_update', 'new_message', 'system'])->default('system');
            $column = $table->longText('title');
            $column = $table->longText('body')->nullable();
            $column = $table->longText('link')->nullable();
            $column = $table->boolean('read')->default(false);
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'notifications_pkey');
        });
        Schema::table('public_notifications', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_notifications_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_notifications', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'read',
  2 => 'created_at',
), 'notifications_user_read_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_notifications');
    }
};
