<?php

namespace App\Services\Services;

use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\Providers\ProviderClientRegistry;
use Illuminate\Support\Facades\DB;

class ServiceImporter
{
    public function __construct(
        private readonly ProviderClientRegistry $clientRegistry,
        private readonly ServicePricingService $pricingService
    ) {
    }

    public function import(Provider $provider): void
    {
        $client = $this->clientRegistry->resolve($provider);
        $services = $client->fetchServices($provider);

        $externalIds = [];

        DB::transaction(function () use ($provider, $services, &$externalIds) {
            foreach ($services as $remoteService) {
                $category = ServiceCategory::query()->firstOrCreate(
                    [
                        'provider_id' => $provider->id,
                        'name' => $remoteService->category,
                    ],
                    [
                        'is_manual_only' => false,
                        'is_active' => true,
                    ]
                );

                /** @var Service $service */
                $service = Service::query()->firstOrNew([
                    'provider_id' => $provider->id,
                    'external_id' => $remoteService->externalId,
                ]);

                $service->service_category_id = $category->id;
                $service->name = $remoteService->name;
                $service->description = $remoteService->description;
                $service->min_quantity = $remoteService->min;
                $service->max_quantity = $remoteService->max;
                $service->cost_price = $remoteService->ratePer1000;
                $service->is_manual = false;
                $service->is_active = true;
                $meta = is_array($service->meta) ? $service->meta : [];
                $meta['provider_payload'] = $remoteService->meta;
                $service->meta = $meta;

                $service->price = $this->pricingService->calculatePrice($service, $service->cost_price);
                $service->save();

                $externalIds[] = $remoteService->externalId;
            }

            $query = Service::query()
                ->where('provider_id', $provider->id)
                ->where('is_manual', false);

            if (count($externalIds)) {
                $query->whereNotIn('external_id', $externalIds);
            }

            $query->update(['is_active' => false]);
        });

        $provider->forceFill(['services_last_synced_at' => now()])->save();
    }
}
