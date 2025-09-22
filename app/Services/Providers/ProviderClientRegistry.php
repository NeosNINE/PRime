<?php

namespace App\Services\Providers;

use App\Models\Provider;
use App\Services\Providers\Contracts\ProviderClient;
use App\Services\Providers\Exceptions\UnknownProviderDriverException;
use Illuminate\Contracts\Container\Container;

class ProviderClientRegistry
{
    public function __construct(private readonly Container $container)
    {
    }

    public function resolve(Provider $provider): ProviderClient
    {
        $drivers = config('service-providers.drivers', []);
        $driver = $provider->driver;

        if (!isset($drivers[$driver])) {
            throw UnknownProviderDriverException::make($driver);
        }

        $client = $this->container->make($drivers[$driver]);

        if (!$client instanceof ProviderClient) {
            throw UnknownProviderDriverException::make($driver);
        }

        return $client;
    }
}
