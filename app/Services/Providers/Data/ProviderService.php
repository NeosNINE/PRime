<?php

namespace App\Services\Providers\Data;

class ProviderService
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly string $category,
        public readonly float $ratePer1000,
        public readonly int $min,
        public readonly int $max,
        public readonly ?string $description = null,
        public readonly array $meta = []
    ) {
    }
}
