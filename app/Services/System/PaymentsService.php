<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\System\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentsService extends Service
{

    use ServiceTrait;


    /**
     * Список транзакций с фильтрами/поиском/сортировкой
     */
    public function getList(Request $request): LengthAwarePaginator
    {
        $search = $request->get('search');
        $method = $request->get('method');
        $status = $request->get('status');

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Transaction::with('user');

        if (!empty($search)) {
            $query->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('login', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%");
            });
        }

        if (!empty($method)) {
            $query->where('method', $method);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($sortBy === 'user') {
            $query->join('users', 'transactions.user_id', '=', 'users.id')
                  ->orderBy('users.login', $sortOrder)
                  ->orderBy('users.email', $sortOrder)
                  ->select('transactions.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate(20)->appends($request->all());
    }


    public function getSortFields(): array
    {
        return [
            'id' => 'ID',
            'user' => 'Пользователь',
            'amount_usd' => 'Сумма',
            'method' => 'Метод',
            'status' => 'Статус',
            'created_at' => 'Дата',
        ];
    }


    /**
     * Принять платеж: меняет статус, начисляет бонус, увеличивает баланс, пишет лог
     */
    public function accept(Transaction $transaction): array
    {
        if ($transaction->status !== 'completed') {
            $transaction->status = 'completed';
            $transaction->save();

            $user = $transaction->user;
            if ($user) {
                $previousBalance = (float) $user->balance;
                $amount = (float) $transaction->amount_usd;
                $bonus = $this->calculateBonus($amount, (array) config('settings.payments.bonuses', []));

                $meta = is_array($transaction->meta) ? $transaction->meta : [];
                if (!isset($meta['bonus_usd']) && $bonus > 0) {
                    $meta['bonus_usd'] = $bonus;
                    $transaction->meta = $meta;
                    $transaction->save();
                } else {
                    $bonus = (float) ($meta['bonus_usd'] ?? 0);
                }

                $user->balance = (float)$user->balance + $amount + $bonus;
                $user->save();

                activity('balance_increase')
                    ->causedBy(Auth::user())
                    ->performedOn($user)
                    ->withProperties([
                        'action' => 'accept',
                        'method' => (string) $transaction->method,
                        'transaction_id' => (int) $transaction->id,
                        'amount_usd' => (float) $amount,
                        'bonus_usd' => (float) $bonus,
                        'previous_balance' => $previousBalance,
                        'new_balance' => (float) $user->balance,
                        'user_id' => (int) $user->id,
                        'user_login' => (string) ($user->login ?? ''),
                    ])
                    ->log('Принят платеж, баланс пополнен');
            }
        }

        return [
            'transaction' => $transaction->fresh(),
            'message' => 'Платеж принят'
        ];
    }


    /**
     * Возврат платежа: списывает баланс при необходимости, ставит статус failed, пишет лог
     */
    public function refund(Transaction $transaction, string $reason = ''): array
    {
        if ($transaction->status === 'completed') {
            $user = $transaction->user;
            if ($user) {
                $previousBalance = (float) $user->balance;
                $user->balance = max(0, (float)$user->balance - (float)$transaction->amount_usd);
                $user->save();

                activity('balance_decrease')
                    ->causedBy(Auth::user())
                    ->performedOn($user)
                    ->withProperties([
                        'action' => 'refund',
                        'method' => (string) $transaction->method,
                        'transaction_id' => (int) $transaction->id,
                        'amount_usd' => (float) $transaction->amount_usd,
                        'reason' => (string) $reason,
                        'previous_balance' => $previousBalance,
                        'new_balance' => (float) $user->balance,
                        'user_id' => (int) $user->id,
                        'user_login' => (string) ($user->login ?? ''),
                    ])
                    ->log('Оформлен возврат, баланс списан');
            }
        }

        $transaction->status = 'failed';
        $meta = is_array($transaction->meta) ? $transaction->meta : [];
        $meta['refund_reason'] = (string)$reason;
        $transaction->meta = $meta;
        $transaction->save();

        return [
            'transaction' => $transaction->fresh(),
            'message' => 'Возврат оформлен'
        ];
    }


    /**
     * Ручное изменение баланса и запись транзакции
     */
    public function changeBalance(int $userId, float $amount, ?string $reason = null): array
    {
        /** @var User $user */
        $user = User::findOrFail($userId);

        $previousBalance = (float) $user->balance;
        $user->balance = max(0, (float)$user->balance + $amount);
        $user->save();

        activity($amount >= 0 ? 'balance_increase' : 'balance_decrease')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'action' => 'manual_change',
                'amount_usd' => (float) $amount,
                'reason' => (string) $reason,
                'previous_balance' => $previousBalance,
                'new_balance' => (float) $user->balance,
                'user_id' => (int) $user->id,
                'user_login' => (string) ($user->login ?? ''),
            ])
            ->log('Ручное изменение баланса админом');

        Transaction::create([
            'user_id' => $user->id,
            'external_id' => null,
            'amount_usd' => abs($amount),
            'method' => 'manual',
            'status' => $amount >= 0 ? 'completed' : 'failed',
            'meta' => ['reason' => (string)$reason],
        ]);

        return [
            'user' => $user,
            'amount' => $amount,
            'message' => 'Баланс обновлён'
        ];
    }


    /**
     * Подсчёт бонуса по конфигу
     */
    protected function calculateBonus(float $amount, array $bonuses): float
    {
        $bonus = 0.0;
        foreach ($bonuses as $rule) {
            $min = (float) ($rule['min'] ?? 0);
            $percent = (float) ($rule['percent'] ?? 0);
            if ($amount >= $min && $percent > 0) {
                $bonus = round($amount * $percent / 100, 2);
                break;
            }
        }
        return $bonus;
    }
}


