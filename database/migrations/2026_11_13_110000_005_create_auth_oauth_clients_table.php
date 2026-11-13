<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_oauth_clients', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->longText('client_secret_hash')->nullable();
            $column = $table->enum('registration_type', ['dynamic', 'manual']);
            $column = $table->longText('redirect_uris');
            $column = $table->longText('grant_types');
            $column = $table->longText('client_name')->nullable();
            $column = $table->longText('client_uri')->nullable();
            $column = $table->longText('logo_uri')->nullable();
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->dateTime('deleted_at')->nullable();
            $column = $table->enum('client_type', ['public', 'confidential'])->default('confidential');
            $column = $table->enum('token_endpoint_auth_method', ['client_secret_basic', 'client_secret_post', 'none']);
            $table->primary(array (
  0 => 'id',
), 'oauth_clients_pkey');
        });
        Schema::table('auth_oauth_clients', function (Blueprint $table): void {
            $table->index(array (
  0 => 'deleted_at',
), 'oauth_clients_deleted_at_idx');
        });
        DB::statement("ALTER TABLE `auth_oauth_clients` ADD CONSTRAINT `oauth_clients_client_name_length` CHECK (CHAR_LENGTH(client_name) <= 1024)");
        DB::statement("ALTER TABLE `auth_oauth_clients` ADD CONSTRAINT `oauth_clients_client_uri_length` CHECK (CHAR_LENGTH(client_uri) <= 2048)");
        DB::statement("ALTER TABLE `auth_oauth_clients` ADD CONSTRAINT `oauth_clients_logo_uri_length` CHECK (CHAR_LENGTH(logo_uri) <= 2048)");
        DB::statement("ALTER TABLE `auth_oauth_clients` ADD CONSTRAINT `oauth_clients_token_endpoint_auth_method_check` CHECK (token_endpoint_auth_method IN ('client_secret_basic', 'client_secret_post', 'none'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_clients');
    }
};
