<?php

namespace App\Jobs\Services;

use App\Models\Provider;
use App\Services\Services\ServicesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProviderServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Provider $provider)
    {
    }

    public function handle(ServicesService $services): void
    {
        $services->importForProvider($this->provider);
    }
}
