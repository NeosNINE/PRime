<?php

namespace App\Console\Commands\Providers;

use App\Jobs\Providers\SyncProviderBalanceJob;
use App\Models\Provider;
use App\Services\Providers\ProviderBalanceSynchronizer;
use Illuminate\Console\Command;

class SyncProviderBalancesCommand extends Command
{
    protected $signature = 'providers:sync-balances {--queue : Dispatch balance sync jobs instead of running inline}';

    protected $description = 'Synchronize balances for all active providers.';

    public function handle(ProviderBalanceSynchronizer $synchronizer): int
    {
        $queue = (bool) $this->option('queue');

        $providers = Provider::query()->active()->get();
        $count = 0;

        foreach ($providers as $provider) {
            if ($queue) {
                SyncProviderBalanceJob::dispatch($provider->id);
            } else {
                $synchronizer->sync($provider);
            }
            $count++;
        }

        $this->info(sprintf('Processed %d provider%s.', $count, $count === 1 ? '' : 's'));

        return Command::SUCCESS;
    }
}
