<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_competitions', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('host_id', 36)->nullable();
            $column = $table->longText('title');
            $column = $table->longText('title_en')->nullable();
            $column = $table->longText('description')->nullable();
            $column = $table->longText('description_en')->nullable();
            $column = $table->string('category', 191);
            $column = $table->longText('location');
            $column = $table->dateTime('starts_at');
            $column = $table->dateTime('ends_at');
            $column = $table->integer('prize_amount')->default('0');
            $column = $table->integer('entry_fee')->default('0');
            $column = $table->integer('capacity')->default('30');
            $column = $table->integer('participants_count')->default('0');
            $column = $table->longText('image_url')->nullable();
            $column = $table->enum('status', ['draft', 'upcoming', 'open', 'closed', 'completed', 'cancelled'])->default('open');
            $column = $table->boolean('featured')->default(false);
            $column = $table->boolean('coming_soon')->default(false);
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'competitions_pkey');
        });
        Schema::table('public_competitions', function (Blueprint $table): void {
            $table->foreign('host_id', 'public_competitions_host_id_foreign')->references('id')->on('auth_users')->nullOnDelete();
        });
        Schema::table('public_competitions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'status',
), 'competitions_status_idx');
        });
        Schema::table('public_competitions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'starts_at',
), 'competitions_starts_at_idx');
        });
        Schema::table('public_competitions', function (Blueprint $table): void {
            $table->index(array (
  0 => 'category',
), 'competitions_category_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_competitions');
    }
};
