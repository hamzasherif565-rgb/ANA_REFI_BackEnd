<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_one_time_tokens', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->enum('token_type', ['confirmation_token', 'reauthentication_token', 'recovery_token', 'email_change_token_new', 'email_change_token_current', 'phone_change_token']);
            $column = $table->string('token_hash', 191);
            $column = $table->string('relates_to', 191);
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->dateTime('expires_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'one_time_tokens_pkey');
        });
        Schema::table('auth_one_time_tokens', function (Blueprint $table): void {
            $table->foreign('user_id', 'auth_one_time_tokens_user_id_foreign')->references('id')->on('auth_users')->cascadeOnDelete();
        });
        Schema::table('auth_one_time_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'token_hash',
), 'one_time_tokens_token_hash_hash_idx');
        });
        Schema::table('auth_one_time_tokens', function (Blueprint $table): void {
            $table->index(array (
  0 => 'relates_to',
), 'one_time_tokens_relates_to_hash_idx');
        });
        Schema::table('auth_one_time_tokens', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'user_id',
  1 => 'token_type',
), 'one_time_tokens_user_id_token_type_key');
        });
        DB::statement("ALTER TABLE `auth_one_time_tokens` ADD CONSTRAINT `one_time_tokens_token_hash_check` CHECK (CHAR_LENGTH(token_hash) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_one_time_tokens');
    }
};
