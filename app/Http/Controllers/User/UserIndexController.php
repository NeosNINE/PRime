<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\System\CurrencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserIndexController extends Controller
{

    public function __construct(){

        $this->middleware('auth');

    }

    public function referralPage(): View
    {

        return view('user.app.pages.referral');
    }

    public function ordersPage(): View
    {

        return view('user.app.pages.orders');
    }

    public function ordersHistoryPage(): View
    {

        return view('user.app.pages.orders-history');
    }

    public function servicesPage(): View
    {

        return view('user.app.pages.services');
    }

    public function balanceTopupPage(): View
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $currentBalance = (float) ($user->balance ?? 0);

        /** @var CurrencyService $currencyService */
        $currencyService = app(CurrencyService::class);
        $currCode = $currencyService->getCurrentCode();
        $currencies = $currencyService->getAvailable();
        $symbol = $currencies[$currCode]['symbol'] ?? '$';
        $converted = number_format($currencyService->convertFromUsd($currentBalance, $currCode), 2, '.', '');

        return view('user.app.pages.balance-topup', [
            'currentBalance' => $currentBalance,
            'currCode' => $currCode,
            'symbol' => $symbol,
            'converted' => $converted,
        ]);
    }

    public function ticketsPage(): View
    {

        return view('user.app.pages.tickets');
    }

    public function updatesPage(): View
    {

        return view('user.app.pages.updates');
    }

    public function apiPage(): View
    {

        return view('user.app.pages.api');
    }

    public function notificationsPage(): View
    {

        return view('user.app.pages.notifications');
    }
}
