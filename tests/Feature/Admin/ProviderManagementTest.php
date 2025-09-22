<?php

namespace Tests\Feature\Admin;

use App\Models\Provider;
use App\Models\System\Role;
use App\Models\User;
use App\Services\Providers\ProviderBalanceSynchronizer;
use Database\Seeders\FirstInitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FirstInitSeeder::class);
    }

    public function test_admin_can_create_provider(): void
    {
        $this->actingAs($this->createAdmin());

        $response = $this->post(route('admin.providers.add.save'), [
            'name' => 'Test Provider',
            'driver' => 'smmflare',
            'api_url' => 'https://example.com',
            'api_key' => 'secret',
            'is_active' => true,
            'low_balance_threshold' => 100,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('providers', [
            'name' => 'Test Provider',
            'driver' => 'smmflare',
            'is_active' => true,
        ]);
    }

    public function test_balance_synchronizer_updates_balance_and_notifies_admins(): void
    {
        $admin = $this->createAdmin();

        $provider = Provider::factory()->create([
            'balance' => 500,
            'currency' => 'USD',
            'low_balance_threshold' => 100,
            'meta' => ['stub_balance' => 50],
        ]);

        $synchronizer = app(ProviderBalanceSynchronizer::class);
        $synchronizer->sync($provider);

        $provider->refresh();

        $this->assertSame(50.0, (float) $provider->balance);
        $this->assertNotNull($provider->last_synced_at);
        $this->assertNotNull($provider->last_balance_notification_at);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'title' => 'Низкий баланс провайдера',
        ]);
    }

    public function test_admin_can_toggle_provider_status_without_full_payload(): void
    {
        $this->actingAs($this->createAdmin());

        $provider = Provider::factory()->create(['is_active' => true]);

        $this->post(route('admin.providers.deactivate', $provider))->assertRedirect();

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'is_active' => 0,
        ]);

        $this->post(route('admin.providers.activate', $provider))->assertRedirect();

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'is_active' => 1,
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
