<?php

namespace Database\\Factories\\System;

use App\\Models\\System\\Provider;
use Illuminate\\Database\\Eloquent\\Factories\\Factory;

/**
 * @extends Factory<Provider>
 */
class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'driver' => $this->faker->slug(),
            'api_url' => $this->faker->url(),
            'api_key' => $this->faker->sha1(),
            'is_active' => true,
        ];
    }
}
