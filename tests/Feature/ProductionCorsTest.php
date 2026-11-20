<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductionCorsTest extends TestCase
{
    public function test_production_frontend_is_allowed_to_preflight_api_requests(): void
    {
        $this->options('/api/v1/auth/login', [], [
            'Origin' => 'https://ana-refi-frontend.vercel.app',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'content-type,authorization',
        ])->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'https://ana-refi-frontend.vercel.app');
    }
}
