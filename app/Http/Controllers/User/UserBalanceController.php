<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\System\Transaction;
use App\Models\System\PromoCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserBalanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Фейковое пополнение: увеличивает баланс в USD на указанную сумму
    public function fakeTopup(Request $request): array
    {
        return userBalance()->fakeTopup($request);
    }

    // Применение промокода — валидация и возврат бонуса, чтобы отобразить на фронте
    public function applyPromo(Request $request): array
    {
        return userBalance()->applyPromo($request);
    }

    // Transactions list for current user (no pagination for now)
    public function listTransactions(Request $request): array
    {
        return userBalance()->listTransactions($request);
    }
}


