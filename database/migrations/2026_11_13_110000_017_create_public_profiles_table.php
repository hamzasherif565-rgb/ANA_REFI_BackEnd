<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_profiles', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->longText('display_name')->nullable();
            $column = $table->longText('avatar_url')->nullable();
            $column = $table->longText('bio')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->enum('account_type', ['traveler', 'host', 'admin'])->default('traveler');
            $column = $table->boolean('is_blocked')->default(false);
            $column = $table->boolean('role_selected')->default(false);
            $column = $table->integer('age')->nullable();
            $column = $table->longText('location')->nullable();
            $column = $table->longText('phone')->nullable();
            $column = $table->longText('email')->nullable();
            $table->primary(array (
  0 => 'id',
), 'profiles_pkey');
        });
        Schema::table('public_profiles', function (Blueprint $table): void {
            $table->foreign('id', 'public_profiles_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        DB::statement("ALTER TABLE `public_profiles` ADD CONSTRAINT `profiles_account_type_check` CHECK (account_type IN ('traveler', 'host', 'admin'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('public_profiles');
    }
};
