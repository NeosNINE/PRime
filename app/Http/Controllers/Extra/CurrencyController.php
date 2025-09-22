<?php

namespace App\Http\Controllers\Extra;

use App\Http\Controllers\Controller;
use App\Services\System\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function list(Request $request, CurrencyService $currencyService): array
    {
        $available = $currencyService->getAvailable();
        $current = $currencyService->getCurrentCode();
        return [
            'current' => $current,
            'available' => $available,
        ];
    }

    public function set(Request $request, CurrencyService $currencyService): array
    {
        $code = (string) $request->input('currency');
        $ok = $currencyService->setCurrentCode($code);

        return [
            'success' => $ok,
            'current' => $currencyService->getCurrentCode(),
        ];
    }
}


