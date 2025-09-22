<?php

namespace App\Extra\DevTools\Services;


use App\Extra\DevTools\Services\Traits\ConsoleCommands;
use App\Extra\DevTools\Services\Traits\Extra;
use App\Extra\DevTools\Services\Traits\Files;
use App\Extra\DevTools\Services\Traits\Helpers;
use App\Extra\DevTools\Services\Traits\Localization;
use App\Extra\DevTools\Services\Traits\Logs;
use App\Extra\DevTools\Services\Traits\Models;
use App\Extra\DevTools\Services\Traits\Navigation;
use App\Extra\DevTools\Services\Traits\Npm;
use App\Extra\DevTools\Services\Traits\Queue;
use App\Extra\DevTools\Services\Traits\Schema;
use App\Extra\DevTools\Services\Traits\SecretConfig;
use App\Extra\DevTools\Services\Traits\SQL;

class DevToolsService
{

    use ConsoleCommands,
        Extra,
        Files,
        Helpers,
        Localization,
        Logs,
        Models,
        Navigation,
        Npm,
        Queue,
        Schema,
        SecretConfig,
        SQL;

}
