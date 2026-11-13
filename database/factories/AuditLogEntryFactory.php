<?php

namespace Database\Factories;

use App\Models\AuditLogEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuditLogEntry> */
class AuditLogEntryFactory extends Factory
{
    protected $model = AuditLogEntry::class;

    public function definition(): array
    {
        return [
            'instance_id' => null,
            'id' => fake()->uuid(),
            'payload' => null,
            'created_at' => null,
            'ip_address' => '',
        ];
    }
}
