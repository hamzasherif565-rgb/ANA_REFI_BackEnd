<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_host_verifications', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->longText('first_name');
            $column = $table->longText('last_name');
            $column = $table->longText('national_id');
            $column = $table->longText('id_front_path')->nullable();
            $column = $table->longText('id_back_path')->nullable();
            $column = $table->string('status', 191)->default('pending');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'host_verifications_pkey');
        });
        Schema::table('public_host_verifications', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_host_verifications_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_host_verifications', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
), 'host_verifications_user_id_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_host_verifications');
    }
};
