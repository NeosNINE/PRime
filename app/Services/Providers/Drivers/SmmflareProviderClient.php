<?php

namespace App\Services\Providers\Drivers;

use App\Models\Provider;
use App\Services\Providers\Contracts\ProviderClient;
use App\Services\Providers\Data\ProviderBalance;
use App\Services\Providers\Data\ProviderService;
use Illuminate\Support\Arr;

class SmmflareProviderClient implements ProviderClient
{
    public function fetchBalance(Provider $provider): ProviderBalance
    {
        $meta = $provider->meta ?? [];
        $balance = (float) Arr::get($meta, 'stub_balance', 1500.0);
        $currency = (string) Arr::get($meta, 'currency', $provider->currency ?? 'USD');

        return new ProviderBalance($balance, $currency, ['stub' => true]);
    }

    public function fetchServices(Provider $provider): array
    {
        $meta = $provider->meta ?? [];
        $rawServices = Arr::get($meta, 'stub_services', [
            [
                'id' => '101',
                'name' => 'Instagram Likes',
                'category' => 'Instagram',
                'rate_per_1k' => 1.20,
                'min' => 50,
                'max' => 10000,
                'description' => 'High quality likes with quick delivery.',
            ],
            [
                'id' => '205',
                'name' => 'YouTube Views',
                'category' => 'YouTube',
                'rate_per_1k' => 2.80,
                'min' => 100,
                'max' => 500000,
                'description' => 'Real views with retention up to 60 seconds.',
            ],
        ]);

        return array_map(function (array $service) {
            return new ProviderService(
                (string) Arr::get($service, 'id'),
                (string) Arr::get($service, 'name'),
                (string) Arr::get($service, 'category'),
                (float) Arr::get($service, 'rate_per_1k', 0),
                (int) Arr::get($service, 'min', 1),
                (int) Arr::get($service, 'max', 1),
                Arr::get($service, 'description'),
                $service
            );
        }, $rawServices);
    }
}
