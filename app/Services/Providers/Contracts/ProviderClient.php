<?php

namespace App\Services\Providers\Contracts;

use App\Models\Provider;
use App\Services\Providers\Data\ProviderBalance;
use App\Services\Providers\Data\ProviderService;

interface ProviderClient
{
    public function fetchBalance(Provider $provider): ProviderBalance;

    /**
     * @return ProviderService[]
     */
    public function fetchServices(Provider $provider): array;
}
