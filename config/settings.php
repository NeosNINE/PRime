<?php

return [
    'users_must_verify_email' => false,
    'languages' => [
        'ru' => [
            'name' => 'Русский'
        ],
        'en' => [
            'name' => 'English'
        ]
    ],
    /* Электронные почты администраторов, на которые отправлять различные уведомления */
    'emails_admins' => [
    ],

    'currency' => [
        'default' => 'usd',
        'available' => [
            'usd' => [ 'code' => 'USD', 'symbol' => '$', 'precision' => 2 ],
            'eur' => [ 'code' => 'EUR', 'symbol' => '€', 'precision' => 2 ],
            'rub' => [ 'code' => 'RUB', 'symbol' => '₽', 'precision' => 2 ],
            'cny' => [ 'code' => 'CNY', 'symbol' => '¥', 'precision' => 2 ],
        ],
        // Курсы конвертации относительно USD
        'rates' => [
            // 1 USD = X currency
            'usd' => 1.0,
            'eur' => 0.85,
            'rub' => 83.26,
            'cny' => 7.11,
            // Платежные валюты методов
            'usdt' => 1.0,
        ],
    ],

    'payments' => [
        // Конфигурация бонусов за пополнение, проверяется от большего к меньшему
        // Пример: при сумме >= 5000 — 10%, >= 1000 — 5%, >= 500 — 2%
        'bonuses' => [
            [ 'min' => 5000, 'percent' => 10 ],
            [ 'min' => 1000, 'percent' => 5 ],
            [ 'min' => 500,  'percent' => 2 ],
        ],
    ],
];
