<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Process;

trait Npm
{

    /**
     * Запустить npm run dev
     */
    public function npmRunDev(): string
    {

        $result = Process::run('npm run dev 2>&1');

        return nl2br($result->output());

    }

}
