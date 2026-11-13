<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_saml_providers', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('sso_provider_id', 36);
            $column = $table->string('entity_id', 191);
            $column = $table->longText('metadata_xml');
            $column = $table->longText('metadata_url')->nullable();
            $column = $table->json('attribute_mapping')->nullable();
            $column = $table->dateTime('created_at')->nullable();
            $column = $table->dateTime('updated_at')->nullable();
            $column = $table->longText('name_id_format')->nullable();
            $table->primary(array (
  0 => 'id',
), 'saml_providers_pkey');
        });
        Schema::table('auth_saml_providers', function (Blueprint $table): void {
            $table->foreign('sso_provider_id', 'auth_saml_providers_sso_provider_id_foreign')->references('id')->on('auth_sso_providers')->cascadeOnDelete();
        });
        Schema::table('auth_saml_providers', function (Blueprint $table): void {
            $table->unique(array (
  0 => 'entity_id',
), 'saml_providers_entity_id_key');
        });
        Schema::table('auth_saml_providers', function (Blueprint $table): void {
            $table->index(array (
  0 => 'sso_provider_id',
), 'saml_providers_sso_provider_id_idx');
        });
        DB::statement("ALTER TABLE `auth_saml_providers` ADD CONSTRAINT `entity_id_not_empty` CHECK (CHAR_LENGTH(entity_id) > 0)");
        DB::statement("ALTER TABLE `auth_saml_providers` ADD CONSTRAINT `metadata_url_not_empty` CHECK (metadata_url = NULL OR CHAR_LENGTH(metadata_url) > 0)");
        DB::statement("ALTER TABLE `auth_saml_providers` ADD CONSTRAINT `metadata_xml_not_empty` CHECK (CHAR_LENGTH(metadata_xml) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_saml_providers');
    }
};
