<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `sessions` MODIFY `user_id` CHAR(36) NULL');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` CHAR(36) NOT NULL');
    }

    public function down(): void
    {
        $hasUuidSessionIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->whereRaw("`user_id` NOT REGEXP '^[0-9]+$'")
            ->exists();
        $hasUuidTokenIds = DB::table('personal_access_tokens')
            ->whereRaw("`tokenable_id` NOT REGEXP '^[0-9]+$'")
            ->exists();

        if ($hasUuidSessionIds || $hasUuidTokenIds) {
            throw new RuntimeException('Cannot restore integer auth IDs while UUID session or token IDs exist.');
        }

        DB::statement('ALTER TABLE `sessions` MODIFY `user_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `personal_access_tokens` MODIFY `tokenable_id` BIGINT UNSIGNED NOT NULL');
    }
};
