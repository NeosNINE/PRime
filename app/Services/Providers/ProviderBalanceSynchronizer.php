<?php

namespace App\Services\Providers;

use App\Models\Provider;
use App\Services\Providers\Data\ProviderBalance;
use App\Support\Notifications\ProviderNotifier;
use Illuminate\Support\Carbon;

class ProviderBalanceSynchronizer
{
    public function __construct(
        private readonly ProviderClientRegistry $registry,
        private readonly ProviderNotifier $notifier
    ) {
    }

    public function sync(Provider $provider): Provider
    {
        $client = $this->registry->resolve($provider);
        $balance = $client->fetchBalance($provider);

        [$shouldNotify, $notificationTimestamp] = $this->evaluateNotificationState($provider, $balance);

        $meta = $provider->meta ?? [];
        $syncedAt = now();
        $meta['last_balance_sync'] = [
            'fetched_at' => $syncedAt->toIso8601String(),
            'payload' => $balance->meta,
        ];

        $provider->forceFill([
            'balance' => $balance->amount,
            'currency' => $balance->currency,
            'last_synced_at' => $syncedAt,
            'meta' => $meta,
            'last_balance_notification_at' => $notificationTimestamp,
        ])->save();

        if ($shouldNotify) {
            $this->notifier->notifyLowBalance($provider->fresh());
        }

        return $provider->fresh();
    }

    public function syncAll(): int
    {
        $count = 0;

        Provider::query()
            ->active()
            ->each(function (Provider $provider) use (&$count) {
                $this->sync($provider);
                $count++;
            });

        return $count;
    }

    private function evaluateNotificationState(Provider $provider, ProviderBalance $balance): array
    {
        $threshold = $provider->low_balance_threshold;
        $now = now();

        if ($threshold === null) {
            return [false, null];
        }

        if ($balance->amount > $threshold) {
            return [false, null];
        }

        $cooldownMinutes = (int) config('service-providers.low_balance_notification_cooldown_minutes', 120);
        $lastNotifiedAt = $provider->last_balance_notification_at;

        if ($lastNotifiedAt instanceof Carbon && $lastNotifiedAt->gt($now->copy()->subMinutes($cooldownMinutes))) {
            return [false, $lastNotifiedAt];
        }

        return [true, $now];
    }
}
