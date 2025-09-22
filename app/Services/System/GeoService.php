<?php

namespace App\Services\System;

use GuzzleHttp\Client;

class GeoService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 3.0,
            'verify' => false,
        ]);
    }

    /**
     * Возвращает массив [city, country] по IP. Если не удалось — nulls
     */
    public function getCityCountry(string $ip): array
    {
        try {
            // Публичное API без ключа, с локализацией на ru
            $resp = $this->http->get('https://ipwho.is/' . urlencode($ip) . '?lang=ru');
            if ($resp->getStatusCode() !== 200) {
                return [null, null];
            }
            $data = json_decode((string)$resp->getBody(), true);
            if (!is_array($data) || empty($data['success'])) {
                return [null, null];
            }
            $city = $data['city'] ?? null;
            $country = $data['country'] ?? null;
            return [$city ?: null, $country ?: null];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }
}


