<?php

namespace Tests\Feature\Admin;

use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\System\Role;
use App\Models\User;
use App\Services\Services\ServiceImporter;
use App\Services\Services\ServiceMarkupsService;
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

    private function createAdmin(): User
    {
        $user = User::factory()->create();
        $roleId = Role::query()->where('key', 'super_admin')->value('id');
        $user->roles()->attach($roleId);

        return $user;
    }
}
