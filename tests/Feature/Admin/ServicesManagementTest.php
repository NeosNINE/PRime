<?php

namespace Tests\Feature\Admin;

use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\System\Role;
use App\Models\User;
use App\Services\Services\ServiceImporter;
use App\Services\Services\ServiceMarkupsService;
use App\Services\Services\ServicesService;
use Database\Seeders\FirstInitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FirstInitSeeder::class);
    }

    public function test_service_importer_creates_services_and_applies_global_markup(): void
    {
        $provider = Provider::factory()->create([
            'meta' => [
                'stub_services' => [
                    [
                        'id' => '501',
                        'name' => 'Test Service',
                        'category' => 'Social',
                        'rate_per_1k' => 2.00,
                        'min' => 50,
                        'max' => 5000,
                        'description' => 'Demo',
                    ],
                ],
            ],
        ]);

        app(ServiceMarkupsService::class)->createOrUpdate([
            'scope' => 'global',
            'percent' => 25,
        ]);

        app(ServiceImporter::class)->import($provider);

        $service = Service::query()->where('provider_id', $provider->id)->where('external_id', '501')->first();

        $this->assertNotNull($service);
        $this->assertSame(2.00, (float) $service->cost_price);
        $this->assertSame(2.50, (float) $service->price);
        $this->assertTrue($service->is_active);
        $this->assertNotNull($provider->fresh()->services_last_synced_at);
    }

    public function test_admin_can_create_manual_service_via_controller(): void
    {
        $this->actingAs($this->createAdmin());

        $category = ServiceCategory::factory()->manual()->create(['name' => 'Manual']);

        $response = $this->post(route('admin.services.add.save'), [
            'name' => 'Manual Service',
            'provider_id' => null,
            'category_id' => $category->id,
            'new_category_name' => null,
            'description' => 'Example description',
            'min_quantity' => 10,
            'max_quantity' => 1000,
            'cost_price' => 1.0,
            'price' => 2.5,
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('services', [
            'name' => 'Manual Service',
            'is_manual' => true,
            'service_category_id' => $category->id,
        ]);
    }

    public function test_markup_form_lists_services_beyond_first_page(): void
    {
        $this->actingAs($this->createAdmin());

        Service::factory()->create(['name' => 'Hidden Service']);
        Service::factory()->count(25)->create();

        $response = $this->get(route('admin.services.browse'));

        $response->assertOk();
        $response->assertSee('Hidden Service', false);
    }

    public function test_importer_does_not_reactivate_manually_disabled_service(): void
    {
        $provider = Provider::factory()->create([
            'meta' => [
                'stub_services' => [
                    [
                        'id' => '9001',
                        'name' => 'Sync Service',
                        'category' => 'General',
                        'rate_per_1k' => 1.00,
                        'min' => 10,
                        'max' => 1000,
                        'description' => 'Stub',
                    ],
                ],
            ],
        ]);

        app(ServiceImporter::class)->import($provider);

        $service = Service::query()->where('provider_id', $provider->id)->where('external_id', '9001')->firstOrFail();

        app(ServicesService::class)->bulkSetStatus([$service->id], false);

        app(ServiceImporter::class)->import($provider);

        $service->refresh();

        $this->assertFalse($service->is_active);
        $this->assertTrue((bool) ($service->meta['admin_disabled'] ?? false));
        $this->assertTrue((bool) ($service->meta['provider_is_active'] ?? false));
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create();
        $roleId = Role::query()->where('key', 'super_admin')->value('id');
        $user->roles()->attach($roleId);

        return $user;
    }
}
