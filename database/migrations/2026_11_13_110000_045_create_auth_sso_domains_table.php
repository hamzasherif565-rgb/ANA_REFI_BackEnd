<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_sso_domains', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('sso_provider_id', 36);
            $column = $table->string('domain', 191);
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $table->primary(array (
  0 => 'id',
), 'sso_domains_pkey');
        });
        Schema::table('auth_sso_domains', function (Blueprint $table): void {
            $table->foreign('sso_provider_id', 'auth_sso_domains_sso_provider_id_foreign')->references('id')->on('auth_sso_providers')->cascadeOnDelete();
        });
        Schema::table('auth_sso_domains', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'domain',
), 'sso_domains_domain_idx');
        });
        Schema::table('auth_sso_domains', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
), 'sso_domains_sso_provider_id_idx');
        });
        DB::statement("ALTER TABLE `auth_sso_domains` ADD CONSTRAINT `domain_not_empty` CHECK (CHAR_LENGTH(domain) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_sso_domains');
    }
};
