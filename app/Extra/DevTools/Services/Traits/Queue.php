<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Artisan;

trait Queue
{

    /**
     * Проверить запушен ли queue демон
     */
    public function queueCheck(): string
    {

        return $this->artisanCommandRun('check:queue');

    }


    /**
     * Запустить queue демона
     */
    public function queueStart(): void
    {

        Artisan::call('queue:restart');
        Artisan::call('queue:work');

    }

}
