<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserExtraController extends Controller
{

    /**
     * Обновление информации с backend для USER
     */
    public function refreshData( Request $request ): array
    {

        if( !Auth::check() )
            return [];

        // Последний полученный клиентом ID события
        $providedLastId = (int)$request->integer('last_event_id');
        $isInitial = $providedLastId <= 0;
        $lastId = $isInitial ? events()->getLastClientId() : $providedLastId;

        // Тянем только события уведомлений (если будем использовать позже)
        $events = [];
        $maxId = $lastId;
        $notifications = [];

        // live-события отключены

        // При первом запросе вернём последние уведомления из БД, чтобы заполнить список
        if ($isInitial) {
            $cacheKey = 'user:'.Auth::id().':notifications:last20';
            $dbList = Cache::remember($cacheKey, 60, function () {
                return \App\Models\System\Notification::query()
                    ->where('user_id', Auth::id())
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();
            });

            foreach ($dbList as $n) {
                $notifications[] = [
                    'id' => 'db-'.$n->id,
                    'title' => (string)$n->title,
                    'text' => (string)($n->text ?? ''),
                    'url' => $n->url,
                    'icon' => (string)($n->icon ?: 'fas fa-info-circle'),
                    'type' => (string)($n->type ?: 'info'),
                    'time' => $n->created_at?->toIso8601String(),
                    'read' => (bool)$n->read_at,
                ];
            }
        }

        $currencyService = app(\App\Services\System\CurrencyService::class);
        $code = $currencyService->getCurrentCode();
        $balanceUsd = (float)Auth::user()->balance;
        $balanceConverted = $currencyService->convertFromUsd($balanceUsd, $code);

        return [
            'csrf_token' => csrf_token(),
            'last_event_id' => $maxId,
            'notifications' => $notifications,
            'balance_usd' => $balanceUsd,
            'balance' => [
                'code' => $code,
                'amount' => $balanceConverted,
                'symbol' => config('settings.currency.available')[$code]['symbol'] ?? '$',
            ],
        ];

    }


    public function markNotificationRead(Request $request): array
    {
        if (!Auth::check()) return ['success' => false];

        $request->validate([
            'id' => ['required', 'string']
        ]);

        $rawId = $request->string('id');
        // id может приходить как 'db-123' или 'ev-456' — работаем только с db-*
        if (str_starts_with($rawId, 'db-')) {
            $id = (int)substr($rawId, 3);
            $n = \App\Models\System\Notification::query()
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
            if ($n && !$n->read_at) {
                $n->read_at = now();
                $n->save();
                \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:last20');
                \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:unread_count');
            }
        }

        return ['success' => true];
    }


    public function deleteNotification(Request $request): array
    {
        if (!Auth::check()) return ['success' => false];

        $request->validate([
            'id' => ['required', 'string']
        ]);

        $rawId = $request->string('id');
        if (str_starts_with($rawId, 'db-')) {
            $id = (int)substr($rawId, 3);
            $n = \App\Models\System\Notification::query()
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
            if ($n) {
                $n->delete();
                \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:last20');
                \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:unread_count');
            }
        }

        return ['success' => true];
    }


    public function markAllNotificationsRead(Request $request): array
    {
        if (!Auth::check()) return ['success' => false];

        \App\Models\System\Notification::query()
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:last20');
        \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:unread_count');

        return ['success' => true];
    }


    public function deleteAllNotifications(Request $request): array
    {
        if (!Auth::check()) return ['success' => false];

        \App\Models\System\Notification::query()
            ->where('user_id', Auth::id())
            ->delete();

        \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:last20');
        \Illuminate\Support\Facades\Cache::forget('user:'.Auth::id().':notifications:unread_count');

        return ['success' => true];
    }


    public function notificationsList(Request $request): array
    {
        if (!Auth::check()) return ['items' => [], 'total' => 0];

        $page = max(1, (int)$request->integer('page'));
        $perPage = min(100, max(1, (int)$request->integer('per_page', 20)));

        $query = \App\Models\System\Notification::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        $total = (int)$query->count();
        $items = $query->forPage($page, $perPage)->get();

        $out = [];
        foreach ($items as $n) {
            $out[] = [
                'id' => 'db-'.$n->id,
                'title' => (string)$n->title,
                'text' => (string)($n->text ?? ''),
                'url' => $n->url,
                'icon' => (string)($n->icon ?: 'fas fa-info-circle'),
                'type' => (string)($n->type ?: 'info'),
                'time' => $n->created_at?->toIso8601String(),
                'read' => (bool)$n->read_at,
            ];
        }

        return [
            'items' => $out,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

}
