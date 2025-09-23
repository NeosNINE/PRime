<?php

namespace App\Services\Providers\Data;

class ProviderBalance
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly array $meta = []
    ) {
    }
}
