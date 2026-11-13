<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_volunteer_service_requests', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('requester_id', 36);
            $column = $table->char('volunteer_id', 36);
            $column = $table->char('volunteer_user_id', 36);
            $column = $table->date('requested_date');
            $column = $table->longText('requested_time')->nullable();
            $column = $table->longText('location');
            $column = $table->longText('reason');
            $column = $table->integer('people_count')->default('1');
            $column = $table->longText('notes')->nullable();
            $column = $table->string('status', 191)->default('pending');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'volunteer_service_requests_pkey');
        });
        Schema::table('public_volunteer_service_requests', function (Blueprint $table): void {
            $table->foreign('requester_id', 'public_volunteer_service_requests_requester_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_volunteer_service_requests', function (Blueprint $table): void {
            $table->foreign('volunteer_id', 'public_volunteer_service_requests_volunteer_id_foreign')->references('id')->on('public_volunteer_requests')->cascadeOnDelete();
        });
        Schema::table('public_volunteer_service_requests', function (Blueprint $table): void {
            $table->foreign('volunteer_user_id', 'public_volunteer_service_requests_volunteer_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_volunteer_service_requests', function (Blueprint $table): void {
            $table->index(array (
  0 => 'volunteer_user_id',
), 'idx_vsr_volunteer_user');
        });
        Schema::table('public_volunteer_service_requests', function (Blueprint $table): void {
            $table->index(array (
  0 => 'requester_id',
), 'idx_vsr_requester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_volunteer_service_requests');
    }
};
