<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_volunteer_requests', function (Blueprint $table): void {
            $column = $table->char('id', 36);
            $column = $table->char('user_id', 36);
            $column = $table->longText('full_name');
            $column = $table->longText('email');
            $column = $table->longText('phone');
            $column = $table->longText('interest_area');
            $column = $table->longText('availability');
            $column = $table->longText('motivation');
            $column = $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $column = $table->dateTime('created_at')->useCurrent();
            $column = $table->dateTime('updated_at')->useCurrent();
            $column = $table->integer('age')->nullable();
            $column = $table->longText('avatar_url')->nullable();
            $column = $table->longText('bio')->nullable();
            $column = $table->longText('location')->nullable();
            $column = $table->json('skills')->default(DB::raw('(JSON_ARRAY())'));
            $column = $table->json('languages')->default(DB::raw('(JSON_ARRAY())'));
            $column = $table->longText('experience')->nullable();
            $column = $table->longText('accessibility')->nullable();
            $column = $table->decimal('rating', 2, 1)->nullable();
            $table->primary(array (
  0 => 'id',
), 'volunteer_requests_pkey');
        });
        Schema::table('public_volunteer_requests', function (Blueprint $table): void {
            $table->index(array (
  0 => 'user_id',
  1 => 'created_at',
), 'volunteer_requests_user_idx');
        });
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_availability_length` CHECK (CHAR_LENGTH(availability) >= 2 AND CHAR_LENGTH(availability) <= 200)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_email_length` CHECK (CHAR_LENGTH(email) >= 3 AND CHAR_LENGTH(email) <= 255)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_interest_length` CHECK (CHAR_LENGTH(interest_area) >= 2 AND CHAR_LENGTH(interest_area) <= 100)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_motivation_length` CHECK (CHAR_LENGTH(motivation) >= 10 AND CHAR_LENGTH(motivation) <= 1500)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_name_length` CHECK (CHAR_LENGTH(full_name) >= 2 AND CHAR_LENGTH(full_name) <= 100)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_phone_length` CHECK (CHAR_LENGTH(phone) >= 10 AND CHAR_LENGTH(phone) <= 20)");
        DB::statement("ALTER TABLE `public_volunteer_requests` ADD CONSTRAINT `volunteer_requests_status_check` CHECK (status IN ('pending', 'approved', 'rejected'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('public_volunteer_requests');
    }
};
