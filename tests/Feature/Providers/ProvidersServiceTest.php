<?php

namespace Tests\\Feature\\Providers;

use App\\Models\\System\\Provider;
use App\\Services\\Providers\\ProvidersService;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class ProvidersServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_status_can_be_toggled_without_full_payload(): void
    {
        $service = new ProvidersService();

        $provider = Provider::factory()->create([
            'name' => 'Example Provider',
            'driver' => 'example-driver',
            'api_url' => 'https://example.test',
            'api_key' => 'secret',
            'is_active' => true,
        ]);

        $service->edit($provider, ['is_active' => false]);
        $this->assertFalse($provider->fresh()->is_active);

        $service->edit($provider, ['is_active' => true]);
        $this->assertTrue($provider->fresh()->is_active);
    }
}
