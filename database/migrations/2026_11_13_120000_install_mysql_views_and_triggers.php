<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $serverVersion = (string) DB::selectOne('SELECT VERSION() AS version')->version;
        $nonceGenerator = stripos($serverVersion, 'mariadb') !== false
            ? 'UNHEX(SUBSTRING(SHA2(CONCAT(UUID(), UUID()), 512), 1, 48))'
            : 'RANDOM_BYTES(24)';

        $this->createTriggerIfMissing('CREATE TRIGGER `ana_auth_users_confirmed_at_default` BEFORE INSERT ON `auth_users` FOR EACH ROW BEGIN IF NEW.`confirmed_at` IS NULL THEN SET NEW.`confirmed_at` = CASE WHEN NEW.`email_confirmed_at` IS NULL THEN NEW.`phone_confirmed_at` WHEN NEW.`phone_confirmed_at` IS NULL THEN NEW.`email_confirmed_at` ELSE LEAST(NEW.`email_confirmed_at`, NEW.`phone_confirmed_at`) END; END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_auth_scim_users_json_defaults` BEFORE INSERT ON `auth_scim_users` FOR EACH ROW BEGIN IF NEW.`user_name` IS NULL OR NEW.`user_name` = \'\' THEN SET NEW.`user_name` = LOWER(JSON_UNQUOTE(JSON_EXTRACT(NEW.`resource`, \'$.userName\'))); END IF; IF NEW.`external_id` IS NULL THEN SET NEW.`external_id` = JSON_UNQUOTE(JSON_EXTRACT(NEW.`resource`, \'$.externalId\')); END IF; IF JSON_CONTAINS_PATH(NEW.`resource`, \'one\', \'$.active\') THEN SET NEW.`active` = JSON_UNQUOTE(JSON_EXTRACT(NEW.`resource`, \'$.active\')) IN (\'true\', \'1\'); END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_storage_objects_path_tokens_default` BEFORE INSERT ON `storage_objects` FOR EACH ROW SET NEW.`path_tokens` = CONCAT(\'[\', REPLACE(JSON_QUOTE(NEW.`name`), \'/\', \'","\'), \']\')');
        $this->createTriggerIfMissing("CREATE TRIGGER `ana_vault_secrets_nonce_default` BEFORE INSERT ON `vault_secrets` FOR EACH ROW BEGIN IF NEW.`nonce` IS NULL THEN SET NEW.`nonce` = {$nonceGenerator}; END IF; END");
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_auth_identities_email_default` BEFORE INSERT ON `auth_identities` FOR EACH ROW BEGIN IF NEW.`email` IS NULL THEN SET NEW.`email` = LOWER(JSON_UNQUOTE(JSON_EXTRACT(NEW.`identity_data`, \'$.email\'))); END IF; END');
        DB::unprepared('CREATE OR REPLACE VIEW `extensions_pg_stat_statements_info` AS SELECT NULL AS `dealloc`, NULL AS `stats_reset` WHERE 1 = 0');
        DB::unprepared('CREATE OR REPLACE VIEW `extensions_pg_stat_statements` AS SELECT NULL AS `userid`, NULL AS `dbid`, NULL AS `toplevel`, NULL AS `queryid`, NULL AS `query`, NULL AS `plans`, NULL AS `total_plan_time`, NULL AS `min_plan_time`, NULL AS `max_plan_time`, NULL AS `mean_plan_time`, NULL AS `stddev_plan_time`, NULL AS `calls`, NULL AS `total_exec_time`, NULL AS `min_exec_time`, NULL AS `max_exec_time`, NULL AS `mean_exec_time`, NULL AS `stddev_exec_time`, NULL AS `rows`, NULL AS `shared_blks_hit`, NULL AS `shared_blks_read`, NULL AS `shared_blks_dirtied`, NULL AS `shared_blks_written`, NULL AS `local_blks_hit`, NULL AS `local_blks_read`, NULL AS `local_blks_dirtied`, NULL AS `local_blks_written`, NULL AS `temp_blks_read`, NULL AS `temp_blks_written`, NULL AS `shared_blk_read_time`, NULL AS `shared_blk_write_time`, NULL AS `local_blk_read_time`, NULL AS `local_blk_write_time`, NULL AS `temp_blk_read_time`, NULL AS `temp_blk_write_time`, NULL AS `wal_records`, NULL AS `wal_fpi`, NULL AS `wal_bytes`, NULL AS `jit_functions`, NULL AS `jit_generation_time`, NULL AS `jit_inlining_count`, NULL AS `jit_inlining_time`, NULL AS `jit_optimization_count`, NULL AS `jit_optimization_time`, NULL AS `jit_emission_count`, NULL AS `jit_emission_time`, NULL AS `jit_deform_count`, NULL AS `jit_deform_time`, NULL AS `stats_since`, NULL AS `minmax_stats_since` WHERE 1 = 0');
        DB::unprepared('CREATE OR REPLACE VIEW `vault_decrypted_secrets` AS SELECT `id`, `name`, `description`, `secret`, CAST(NULL AS CHAR) AS `decrypted_secret`, `key_id`, `nonce`, `created_at`, `updated_at` FROM `vault_secrets`');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_competitions_updated_at` BEFORE UPDATE ON `public_competitions` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_updated_at` BEFORE UPDATE ON `public_bookings` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_listings_updated_at` BEFORE UPDATE ON `public_listings` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_user_contacts_updated_at` BEFORE UPDATE ON `public_user_contacts` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_host_verifications_updated_at` BEFORE UPDATE ON `public_host_verifications` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_feature_requests_updated_at` BEFORE UPDATE ON `public_feature_requests` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_volunteer_requests_updated_at` BEFORE UPDATE ON `public_volunteer_requests` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_volunteer_service_requests_updated_at` BEFORE UPDATE ON `public_volunteer_service_requests` FOR EACH ROW SET NEW.`updated_at` = CURRENT_TIMESTAMP');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_auth_on_auth_user_created` AFTER INSERT ON `auth_users` FOR EACH ROW INSERT IGNORE INTO `public_profiles` (`id`) VALUES (NEW.`id`)');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_notify_insert` AFTER INSERT ON `public_bookings` FOR EACH ROW BEGIN IF NEW.`host_id` IS NOT NULL THEN INSERT INTO `public_notifications` (`id`, `user_id`, `type`, `title`, `body`, `read`, `created_at`) VALUES (UUID(), NEW.`host_id`, \'booking_status\', CONCAT(\'Booking \', NEW.`status`), CONCAT(\'Booking status: \', NEW.`status`), 0, CURRENT_TIMESTAMP); END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_notify_update` AFTER UPDATE ON `public_bookings` FOR EACH ROW BEGIN IF NEW.`host_id` IS NOT NULL AND NOT (NEW.`status` <=> OLD.`status`) THEN INSERT INTO `public_notifications` (`id`, `user_id`, `type`, `title`, `body`, `read`, `created_at`) VALUES (UUID(), NEW.`host_id`, \'booking_status\', CONCAT(\'Booking \', NEW.`status`), CONCAT(\'Booking status: \', NEW.`status`), 0, CURRENT_TIMESTAMP); END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_feature_requests_notify_insert` AFTER INSERT ON `public_feature_requests` FOR EACH ROW BEGIN IF NEW.`host_id` IS NOT NULL THEN INSERT INTO `public_notifications` (`id`, `user_id`, `type`, `title`, `body`, `read`, `created_at`) VALUES (UUID(), NEW.`host_id`, \'system\', \'Feature request received\', CONCAT(\'Feature request status: \', NEW.`status`), 0, CURRENT_TIMESTAMP); END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_feature_requests_notify_update` AFTER UPDATE ON `public_feature_requests` FOR EACH ROW BEGIN IF NEW.`host_id` IS NOT NULL AND NOT (NEW.`status` <=> OLD.`status`) THEN INSERT INTO `public_notifications` (`id`, `user_id`, `type`, `title`, `body`, `read`, `created_at`) VALUES (UUID(), NEW.`host_id`, \'system\', \'Feature request updated\', CONCAT(\'Feature request status: \', NEW.`status`), 0, CURRENT_TIMESTAMP); END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_prepare_competition_insert` BEFORE INSERT ON `public_bookings` FOR EACH ROW BEGIN IF NEW.`item_type` = \'competition\' AND NOT EXISTS (SELECT 1 FROM `public_competitions` WHERE `id` = NEW.`item_id`) THEN SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Booking references a missing competition\'; END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_prepare_competition_update` BEFORE UPDATE ON `public_bookings` FOR EACH ROW BEGIN IF NEW.`item_type` = \'competition\' AND NOT EXISTS (SELECT 1 FROM `public_competitions` WHERE `id` = NEW.`item_id`) THEN SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Booking references a missing competition\'; END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_sync_competition_insert` AFTER INSERT ON `public_bookings` FOR EACH ROW BEGIN IF NEW.`item_type` = \'competition\' AND NEW.`status` IN (\'confirmed\', \'completed\') THEN UPDATE `public_competitions` SET `participants_count` = `participants_count` + 1 WHERE `id` = NEW.`item_id`; END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_sync_competition_delete` AFTER DELETE ON `public_bookings` FOR EACH ROW BEGIN IF OLD.`item_type` = \'competition\' AND OLD.`status` IN (\'confirmed\', \'completed\') THEN UPDATE `public_competitions` SET `participants_count` = GREATEST(0, `participants_count` - 1) WHERE `id` = OLD.`item_id`; END IF; END');
        $this->createTriggerIfMissing('CREATE TRIGGER `ana_public_bookings_sync_competition_update` AFTER UPDATE ON `public_bookings` FOR EACH ROW BEGIN IF OLD.`item_type` = \'competition\' AND OLD.`status` IN (\'confirmed\', \'completed\') AND NOT (NEW.`item_type` = \'competition\' AND NEW.`status` IN (\'confirmed\', \'completed\') AND NEW.`item_id` = OLD.`item_id`) THEN UPDATE `public_competitions` SET `participants_count` = GREATEST(0, `participants_count` - 1) WHERE `id` = OLD.`item_id`; END IF; IF NEW.`item_type` = \'competition\' AND NEW.`status` IN (\'confirmed\', \'completed\') AND NOT (OLD.`item_type` = \'competition\' AND OLD.`status` IN (\'confirmed\', \'completed\') AND NEW.`item_id` = OLD.`item_id`) THEN UPDATE `public_competitions` SET `participants_count` = `participants_count` + 1 WHERE `id` = NEW.`item_id`; END IF; END');
    }

    private function createTriggerIfMissing(string $sql): void
    {
        if (! preg_match('/^CREATE TRIGGER `([^`]+)`/i', $sql, $matches)) {
            throw new RuntimeException('Unable to determine trigger name from SQL.');
        }

        $exists = DB::table('information_schema.triggers')
            ->where('trigger_schema', DB::connection()->getDatabaseName())
            ->where('trigger_name', $matches[1])
            ->exists();

        if (! $exists) {
            DB::unprepared($sql);
        }
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_auth_users_confirmed_at_default`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_auth_scim_users_json_defaults`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_storage_objects_path_tokens_default`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_vault_secrets_nonce_default`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_auth_identities_email_default`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_competitions_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_listings_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_user_contacts_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_host_verifications_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_feature_requests_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_volunteer_requests_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_volunteer_service_requests_updated_at`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_auth_on_auth_user_created`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_notify_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_notify_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_feature_requests_notify_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_feature_requests_notify_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_prepare_competition_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_prepare_competition_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_sync_competition_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_sync_competition_delete`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ana_public_bookings_sync_competition_update`');
        DB::unprepared('DROP VIEW IF EXISTS `vault_decrypted_secrets`');
        DB::unprepared('DROP VIEW IF EXISTS `extensions_pg_stat_statements`');
        DB::unprepared('DROP VIEW IF EXISTS `extensions_pg_stat_statements_info`');
    }
};
