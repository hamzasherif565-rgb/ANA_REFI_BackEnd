<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_user_roles', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->enum('role', ['user', 'host', 'admin'])->default('user');
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'user_roles_pkey');
        });
        Schema::table('public_user_roles', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_user_roles_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('public_user_roles', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'role',
), 'user_roles_user_id_role_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_user_roles');
    }
};
