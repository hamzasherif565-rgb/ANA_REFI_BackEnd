<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->nullable()->constrained('public_bookings')->restrictOnDelete();
            $table->foreignUuid('feature_request_id')->nullable()->constrained('public_feature_requests')->restrictOnDelete();
            $table->string('provider', 30)->default('paymob');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('amount_cents');
            $table->char('currency', 3)->default('EGP');
            $table->string('intention_id')->nullable()->unique();
            $table->string('paymob_order_id')->nullable()->unique();
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamps();
            $table->index(['booking_id', 'status']);
            $table->index(['feature_request_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
