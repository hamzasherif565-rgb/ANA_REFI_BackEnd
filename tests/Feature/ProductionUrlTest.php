<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionUrlTest extends TestCase
{
    public function test_production_generated_urls_use_https(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('https://localhost/api/v1/volunteers?page=1', URL::to('/api/v1/volunteers?page=1'));

        URL::forceScheme(null);
    }
}
