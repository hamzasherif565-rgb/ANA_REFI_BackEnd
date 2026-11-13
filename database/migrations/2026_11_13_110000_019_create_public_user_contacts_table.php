<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_user_contacts', function (Blueprint $table): void {
            $column = $table->char('user_id', 36);
            $column = $table->longText('phone')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $table->primary(array (
  0 => 'user_id',
), 'user_contacts_pkey');
        });
        Schema::table('public_user_contacts', function (Blueprint $table): void {
            $table->foreign('user_id', 'public_user_contacts_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_user_contacts');
    }
};
