<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\System\Role;
use App\Models\User;
use Database\Seeders\FirstInitSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrdersManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FirstInitSeeder::class);
    }

    public function test_manual_order_status_change_triggers_refund(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $user = User::factory()->create(['balance' => 0]);
        $category = ServiceCategory::factory()->manual()->create();
        $service = Service::factory()->manual()->create([
            'service_category_id' => $category->id,
            'provider_id' => null,
            'price' => 10,
            'cost_price' => 5,
        ]);

        $order = Order::factory()->manual()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'provider_id' => null,
            'price' => 10,
            'cost_price' => 5,
            'status' => 'in_progress',
            'is_drip_feed' => false,
        ]);

        $response = $this->put(route('admin.orders.update_status', $order), [
            'status' => 'failed',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $user->refresh();

        $this->assertSame('failed', $order->status);
        $this->assertNotNull($order->refunded_at);
        $this->assertSame(10.0, (float) $order->refunded_amount);
        $this->assertSame(10.0, (float) $user->balance);
    }

    public function test_orders_export_requires_dedicated_permission(): void
    {
        $restrictedUser = $this->createUserWithAccess(['orders.browse']);
        $this->actingAs($restrictedUser);

        $this->get(route('admin.orders.export'))->assertStatus(403);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.orders.export'))->assertOk();
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create();
        $roleId = Role::query()->where('key', 'super_admin')->value('id');
        $user->roles()->attach($roleId);

        return $user;
    }

    private function createUserWithAccess(array $access): User
    {
        $role = Role::query()->forceCreate([
            'name' => 'Temp role',
            'key' => 'temp_role_' . Str::random(6),
            'access' => $access,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        return $user;
    }
}
