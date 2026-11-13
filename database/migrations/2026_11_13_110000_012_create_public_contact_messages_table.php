<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_contact_messages', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->longText('name');
            $column = $table->longText('email');
            $column = $table->longText('message');
            $column = $table->dateTime('created_at')->useCurrent();
            $table->primary(array (
  0 => 'id',
), 'contact_messages_pkey');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_contact_messages');
    }
};
