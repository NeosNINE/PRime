<?php

namespace App\Services\Services;

use App\Models\Service;
use App\Models\ServiceMarkup;
use Illuminate\Support\Collection;

class ServicePricingService
{
    public function calculatePrice(Service $service, float $baseCost): float
    {
        $markups = $this->resolveMarkups($service);

        $percent = $markups->sum(fn (ServiceMarkup $markup) => $markup->percent ?? 0.0);
        $fixed = $markups->sum(fn (ServiceMarkup $markup) => $markup->fixed ?? 0.0);

        $price = $baseCost;
        if ($percent) {
            $price += $baseCost * ($percent / 100);
        }
        if ($fixed) {
            $price += $fixed;
        }

        return round(max($price, 0), 4);
    }

    protected function resolveMarkups(Service $service): Collection
    {
        $query = ServiceMarkup::query()
            ->where('scope', 'global');

        if ($service->provider_id) {
            $query->orWhere(fn ($q) => $q
                ->where('scope', 'provider')
                ->where('provider_id', $service->provider_id));
        }

        if ($service->service_category_id) {
            $query->orWhere(fn ($q) => $q
                ->where('scope', 'category')
                ->where('service_category_id', $service->service_category_id));
        }

        if ($service->id) {
            $query->orWhere(fn ($q) => $q
                ->where('scope', 'service')
                ->where('service_id', $service->id));
        }

        return $query->get();
    }
}
