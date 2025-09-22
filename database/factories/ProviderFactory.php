<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' API',
            'driver' => 'smmflare',
            'api_url' => 'https://api.example.com',
            'api_key' => $this->faker->uuid,
            'is_active' => true,
            'balance' => $this->faker->randomFloat(4, 0, 1000),
            'currency' => 'USD',
            'last_synced_at' => now(),
            'low_balance_threshold' => 50,
            'meta' => [],
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
