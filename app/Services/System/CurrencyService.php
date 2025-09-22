<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class CurrencyService
{
    public const SESSION_KEY = 'currency';

    public function getAvailable(): array
    {
        return (array) Config::get('settings.currency.available', []);
    }

    public function getDefaultCode(): string
    {
        $default = (string) Config::get('settings.currency.default', 'usd');
        return strtolower($default);
    }

    public function getCurrentCode(): string
    {
        $code = (string) Session::get(self::SESSION_KEY, $this->getDefaultCode());
        $code = strtolower($code);

        if (!array_key_exists($code, $this->getAvailable())) {
            $code = $this->getDefaultCode();
            Session::put(self::SESSION_KEY, $code);
        }

        return $code;
    }

    public function getRate(string $code): float
    {
        $code = strtolower($code);
        $rates = (array) Config::get('settings.currency.rates', []);
        $rate = (float) ($rates[$code] ?? 1.0);
        return $rate > 0 ? $rate : 1.0;
    }

    // Конвертация из USD в выбранную валюту
    public function convertFromUsd(float $amountUsd, ?string $toCode = null): float
    {
        $code = strtolower($toCode ?: $this->getCurrentCode());
        $rate = $this->getRate($code);
        return round($amountUsd * $rate, 2);
    }

    /**
     * Подготовка данных для блока валют/баланса в шапке
     */
    public function buildHeaderCurrencyData(?User $user): array
    {
        $currentCode = $this->getCurrentCode();
        $currencies = (array) Config::get('settings.currency.available', []);
        $rates = (array) Config::get('settings.currency.rates', []);
        $balanceUsd = $user ? (float) ($user->balance ?? 0) : 0.0;
        $currentSymbol = $currencies[$currentCode]['symbol'] ?? '$';
        $currentAmount = number_format($this->convertFromUsd($balanceUsd, $currentCode), 2, '.', '');

        $options = [];
        foreach ($currencies as $code => $c) {
            $options[] = [
                'code' => $code,
                'symbol' => $c['symbol'] ?? '$',
                'name' => strtoupper($c['code'] ?? $code),
                'rate' => (float) ($rates[$code] ?? 1),
                'amount' => number_format($this->convertFromUsd($balanceUsd, $code), 2, '.', ''),
                'active' => $code === $currentCode,
            ];
        }

        return [
            'current_code' => $currentCode,
            'current_symbol' => $currentSymbol,
            'current_amount' => $currentAmount,
            'options' => $options,
        ];
    }

    public function getCurrency(string $code): ?array
    {
        $code = strtolower(trim($code));
        $available = $this->getAvailable();
        return $available[$code] ?? null;
    }

    public function setCurrentCode(string $code): bool
    {
        $code = strtolower(trim($code));
        if (!array_key_exists($code, $this->getAvailable())) {
            return false;
        }
        Session::put(self::SESSION_KEY, $code);
        return true;
    }
}


