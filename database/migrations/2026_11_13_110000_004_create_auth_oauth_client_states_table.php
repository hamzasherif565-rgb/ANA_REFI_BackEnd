<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_oauth_client_states', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->longText('provider_type');
            $column = $table->longText('code_verifier')->nullable();
            $column = $table->dateTime('created_at');
            $table->primary(array (
  0 => 'id',
), 'oauth_client_states_pkey');
        });
        Schema::table('auth_oauth_client_states', function (Blueprint $table): void {
            $table->index(array (
  0 => 'created_at',
), 'idx_oauth_client_states_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_oauth_client_states');
    }
};
