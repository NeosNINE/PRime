<?php

namespace App\Jobs\Providers;

use App\Models\Provider;
use App\Services\Providers\ProviderBalanceSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProviderBalanceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $providerId)
    {
    }

    public function handle(ProviderBalanceSynchronizer $synchronizer): void
    {
        $provider = Provider::query()->find($this->providerId);

        if (!$provider || !$provider->is_active) {
            return;
        }

        $synchronizer->sync($provider);
    }

    public function tags(): array
    {
        return ['providers', 'provider:' . $this->providerId];
    }
}
