<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory([
                'service_category_id' => ServiceCategory::factory(),
                'provider_id' => Provider::factory(),
            ]),
            'provider_id' => Provider::factory(),
            'external_id' => (string) $this->faker->numberBetween(10000, 99999),
            'link' => $this->faker->url(),
            'quantity' => 100,
            'price' => 5.00,
            'cost_price' => 3.00,
            'status' => 'pending',
            'is_drip_feed' => false,
            'drip_runs' => null,
            'drip_interval_minutes' => null,
            'drip_runs_processed' => 0,
            'is_manual' => false,
            'meta' => [],
        ];
    }

    public function manual(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_manual' => true,
                'provider_id' => $attributes['provider_id'] ?? null,
            ];
        });
    }
}
