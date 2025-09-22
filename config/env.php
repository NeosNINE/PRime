<?php

return [
    'app_url' => env('APP_URL'),
    'app_key' => env('APP_KEY'),
    'db' => [
        'connection' => env('DB_CONNECTION'),
        'host' => env('DB_HOST'),
        'port' => env('DB_PORT'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD')
    ],
    'DEBUGBAR_ENABLED' => env('DB_DEBUGBAR_ENABLED', true),
];
