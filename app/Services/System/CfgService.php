<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use App\Models\System\Cfg;


class CfgService extends Service
{

    public function get( string $key ): mixed
    {

        return Cfg::where('key', $key)->first()->value ?? null;

    }

    public function set( string $key, $value ): void
    {

        Cfg::updateOrInsert([
            'key' => $key
        ], [
            'key' => $key,
            'value' => $value
        ]);

    }

}
