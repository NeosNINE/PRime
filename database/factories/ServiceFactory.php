<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'provider_id' => Provider::factory(),
            'external_id' => (string) $this->faker->numberBetween(1000, 9999),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'min_quantity' => 10,
            'max_quantity' => 1000,
            'cost_price' => 1.50,
            'price' => 2.50,
            'is_active' => true,
            'is_manual' => false,
            'total_orders' => 0,
            'meta' => [],
        ];
    }

    public function manual(): self
    {
        return $this->state(function () {
            return [
                'provider_id' => null,
                'external_id' => null,
                'is_manual' => true,
            ];
        });
    }
}
