<?php

namespace Database\Seeders;

use App\Models\AuditLogEntry;
use Illuminate\Database\Seeder;

class AuditLogEntrySeeder extends Seeder
{
    public function run(): void
    {
        $remaining = max(0, 5 - AuditLogEntry::query()->count());
        if ($remaining > 0) {
            AuditLogEntry::factory()->count($remaining)->create();
        }
    }
}
