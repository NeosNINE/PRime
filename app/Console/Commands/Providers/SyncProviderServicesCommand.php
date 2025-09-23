<?php

namespace App\Console\Commands\Providers;

use App\Jobs\Services\SyncProviderServicesJob;
use App\Models\Provider;
use Illuminate\Console\Command;

class SyncProviderServicesCommand extends Command
{
    protected $signature = 'providers:sync-services {--queue : Dispatch jobs to the queue instead of running synchronously}';

    protected $description = 'Synchronize services and pricing from all active providers.';

    public function handle(): int
    {
        $providers = Provider::query()->where('is_active', true)->get();

        if ($providers->isEmpty()) {
            $this->info('No active providers found.');
            return self::SUCCESS;
        }

        $queued = (bool) $this->option('queue');

        foreach ($providers as $provider) {
            if ($queued) {
                SyncProviderServicesJob::dispatch($provider);
            } else {
                SyncProviderServicesJob::dispatchSync($provider);
            }
        }

        $this->info('Service synchronization initiated for ' . $providers->count() . ' provider(s).');

        return self::SUCCESS;
    }
}
