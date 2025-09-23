<?php

return [
    'drivers' => [
        'smmflare' => App\Services\Providers\Drivers\SmmflareProviderClient::class,
    ],

    'labels' => [
        'smmflare' => 'SMMFlare (stub)',
    ],

    'low_balance_notification_cooldown_minutes' => 120,
];
