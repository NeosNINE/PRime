<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Models\Order;
use App\Models\Provider;
use App\Models\Service;
use App\Services\Services\OrdersService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrdersController extends Controller
{
    public function __construct(private readonly OrdersService $orders)
    {
    }

    public function browse(Request $request): View
    {
        roles()->checkAccessWithAbort('orders.browse');

        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'drip_feed' => $request->input('drip_feed', ''),
            'service_id' => $request->input('service_id'),
            'provider_id' => $request->input('provider_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $orders = $this->orders->list($filters);

        return view('admin.app.orders.browse', [
            'orders' => $orders,
            'filters' => $filters,
            'services' => Service::orderBy('name')->get(),
            'providers' => Provider::orderBy('name')->get(),
        ]);
    }

    public function read(Order $order): View
    {
        roles()->checkAccessWithAbort('orders.read');

        return view('admin.app.orders.read', [
            'order' => $order->load(['user', 'service.category', 'provider', 'runs']),
        ]);
    }

    public function updateStatus(OrderStatusUpdateRequest $request, Order $order): RedirectResponse
    {
        roles()->checkAccessWithAbort('orders.edit');

        $this->orders->changeStatus($order, $request->validated()['status'], $request->validated()['comment'] ?? null);

        return back()->with('success', 'Статус заказа обновлен.');
    }

    public function export(Request $request)
    {
        roles()->checkAccessWithAbort('orders.browse');

        $data = $this->orders->export($request->all());

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Пользователь', 'Услуга', 'Ссылка', 'Количество', 'Статус', 'Drip-feed', 'Создан']);
            foreach ($data as $order) {
                fputcsv($handle, [
                    $order->id,
                    optional($order->user)->login ?? '',
                    optional($order->service)->name ?? '',
                    $order->link,
                    $order->quantity,
                    $order->status,
                    $order->is_drip_feed ? 'Yes' : 'No',
                    $order->created_at?->toDateTimeString(),
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, 'orders.csv');
    }
}
