<?php

namespace App\Services\Services;

use App\Models\Services\Service;
use Illuminate\Support\Arr;

class ServiceImporter
{
    public function import(array $services): void
    {
        foreach ($services as $payload) {
            $externalId = Arr::get($payload, 'id');

            if ($externalId === null) {
                continue;
            }

            $service = Service::query()->firstOrNew([
                'external_id' => (string) $externalId,
            ]);

            if (Arr::has($payload, 'name')) {
                $service->name = Arr::get($payload, 'name');
            }

            $meta = $service->meta;
            $previousProviderPayload = $meta['provider_payload'] ?? [];
            $previousProviderAvailability = $this->extractAvailability($previousProviderPayload);

            $meta['provider_payload'] = $this->mergeProviderPayload($previousProviderPayload, $payload);
            $service->meta = $meta;

            $currentProviderAvailability = $this->extractAvailability($service->meta['provider_payload']);
            $availabilityFlagProvided = $this->hasAvailabilityFlag($payload);

            if (! $service->exists) {
                $service->is_active = $currentProviderAvailability !== false;
            } elseif ($availabilityFlagProvided) {
                if ($currentProviderAvailability === true && $previousProviderAvailability === false) {
                    $service->is_active = true;
                }

                if ($currentProviderAvailability === false) {
                    $service->is_active = false;
                }
            }

            $service->save();
        }
    }

    private function extractAvailability(array $payload): ?bool
    {
        foreach (['available', 'is_available', 'is_active'] as $key) {
            if (array_key_exists($key, $payload)) {
                $value = $payload[$key];

                if ($value === null) {
                    return null;
                }

                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $value;
            }
        }

        return null;
    }

    private function hasAvailabilityFlag(array $payload): bool
    {
        foreach (['available', 'is_available', 'is_active'] as $key) {
            if (array_key_exists($key, $payload)) {
                return true;
            }
        }

        return false;
    }

    private function mergeProviderPayload(array $previous, array $current): array
    {
        foreach ($current as $key => $value) {
            $previous[$key] = $value;
        }

        return $previous;
    }
}
