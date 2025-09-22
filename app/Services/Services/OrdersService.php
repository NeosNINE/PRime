<?php

namespace App\Services\Services;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\Order;
use App\Models\Service as ServiceModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersService extends Service
{
    use ServiceTrait;

    protected ?string $model_key = 'Order';

    public function list(array $filters): LengthAwarePaginator
    {
        $query = $this->applyFilters(Order::query()->with(['user', 'service.category', 'provider']), $filters);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'created_at' => 'created_at',
            'status' => 'status',
        ];

        $query->orderBy($allowedSorts[$sortBy] ?? 'created_at', $sortOrder);
        if (($allowedSorts[$sortBy] ?? 'created_at') !== 'created_at') {
            $query->orderByDesc('created_at');
        }

        return $query->paginate(25)->appends($filters);
    }

    public function changeStatus(Order $order, string $status, ?string $comment = null): Order
    {
        if (!$order->is_manual) {
            throw new \RuntimeException('Изменение статуса доступно только для ручных заказов.');
        }

        $status = strtolower($status);
        $allowed = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Недопустимый статус заказа.');
        }

        return DB::transaction(function () use ($order, $status, $comment) {
            $previousStatus = $order->status;
            $order->status = $status;
            $order->meta = array_merge($order->meta ?? [], [
                'admin_comment' => $comment,
            ]);

            if ($status === 'completed') {
                $order->completed_at = $order->completed_at ?? now();
                $order->failed_at = null;
                if ($previousStatus !== 'completed') {
                    $this->incrementServiceOrders($order->service);
                }
            } elseif ($status === 'failed') {
                $order->failed_at = now();
                $order->completed_at = null;
                $this->refundOrder($order);
            } else {
                $order->completed_at = null;
                if ($status !== 'failed') {
                    $order->failed_at = null;
                }
            }

            $order->save();

            return $order->fresh(['user', 'service', 'provider']);
        });
    }

    public function export(array $filters): EloquentCollection
    {
        return $this->applyFilters(Order::query()->with(['user', 'service', 'provider']), $filters)
            ->orderByDesc('created_at')
            ->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('id', $search)
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('login', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('service', fn (Builder $serviceQuery) => $serviceQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (!empty($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (Arr::exists($filters, 'drip_feed') && $filters['drip_feed'] !== '') {
            $query->where('is_drip_feed', (bool) $filters['drip_feed']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    protected function refundOrder(Order $order): void
    {
        if ($order->refunded_at || $order->price <= 0) {
            return;
        }

        $user = $order->user()->lockForUpdate()->first();
        if (!$user) {
            return;
        }

        $previousBalance = (float) $user->balance;
        $user->balance = (float) $user->balance + (float) $order->price;
        $user->save();

        $order->refunded_at = now();
        $order->refunded_amount = $order->price;

        activity('balance_increase')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'action' => 'order_refund',
                'order_id' => $order->id,
                'amount_usd' => (float) $order->price,
                'previous_balance' => $previousBalance,
                'new_balance' => (float) $user->balance,
            ])
            ->log('Возврат средств за невыполненный заказ');
    }

    protected function incrementServiceOrders(?ServiceModel $service): void
    {
        if ($service) {
            $service->increment('total_orders');
        }
    }
}
