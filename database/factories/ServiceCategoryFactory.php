<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCategoryFactory extends Factory
{
    protected $model = ServiceCategory::class;

    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'name' => $this->faker->words(2, true),
            'is_manual_only' => false,
            'is_active' => true,
        ];
    }

    public function manual(): self
    {
        return $this->state(function () {
            return [
                'provider_id' => null,
                'is_manual_only' => true,
            ];
        });
    }
}
