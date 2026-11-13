<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_feature_requests', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('listing_id', 36);
            $column = $table->char('host_id', 36);
            $column = $table->integer('amount')->default('500');
            $column = $table->string('payment_method', 191)->default('visa');
            $column = $table->string('payment_status', 191)->default('paid');
            $column = $table->string('status', 191)->default('pending');
            $column = $table->longText('admin_note')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'feature_requests_pkey');
        });
        Schema::table('public_feature_requests', function (Blueprint $table): void {
            $table->foreign('host_id', 'public_feature_requests_host_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_feature_requests', function (Blueprint $table): void {
            $table->foreign('listing_id', 'public_feature_requests_listing_id_foreign')->references('id')->on('public_listings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_feature_requests');
    }
};
