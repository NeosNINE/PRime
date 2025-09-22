<?php

namespace Tests\Feature\Admin;

use App\Models\Services\Service;
use App\Services\Services\ServiceImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_deactivation_survives_import(): void
    {
        $importer = new ServiceImporter();

        $payload = [
            [
                'id' => 'srv-1',
                'name' => 'Example service',
                'available' => true,
            ],
        ];

        $importer->import($payload);

        $service = Service::query()->firstOrFail();
        $this->assertTrue($service->is_active);
        $this->assertSame($payload[0], $service->meta['provider_payload']);

        $service->update(['is_active' => false]);

        $importer->import([
            [
                'id' => 'srv-1',
                'name' => 'Example service',
            ],
        ]);

        $service->refresh();
        $this->assertFalse($service->is_active);
        $this->assertSame('Example service', $service->name);
        $this->assertSame('srv-1', $service->external_id);
    }
}
