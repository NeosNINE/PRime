@extends('admin.app.layout')
@section('title', 'Заказы' )

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    <div class="container">
        <div class="page-header">
            <h1>Заказы</h1>
            <div class="actions">
                {!! admin()->buttons([
                    [
                        'href' => route('admin.orders.export', request()->all()),
                        'style' => 'secondary',
                        'icon' => 'fa fa-download',
                        'text' => 'Экспорт CSV',
                        'access' => 'orders.browse'
                    ]
                ]) !!}
            </div>
        </div>

        <div class="box no-padding">
            <div class="box-filters">
                <form class="filters-inline" method="GET" action="">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Поиск</label>
                            <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="ID, пользователь или услуга">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Статус</label>
                            <select name="status" class="form-select select2">
                                <option value="">Любой</option>
                                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'failed' => 'Failed', 'cancelled' => 'Cancelled'] as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" @selected($filters['status'] === $statusKey)>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Услуга</label>
                            <select name="service_id" class="form-select select2">
                                <option value="">Все</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" @selected($filters['service_id'] == $service->id)>{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Провайдер</label>
                            <select name="provider_id" class="form-select select2">
                                <option value="">Все</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" @selected($filters['provider_id'] == $provider->id)>{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Drip-feed</label>
                            <select name="drip_feed" class="form-select">
                                <option value="">Все</option>
                                <option value="1" @selected($filters['drip_feed'] === '1')>Да</option>
                                <option value="0" @selected($filters['drip_feed'] === '0')>Нет</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Дата с</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Дата по</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Фильтр</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="box-table">
                <table class="table table-striped orders-table @if(!$orders->count()) table-no-rows-found @endif">
                    <thead>
                    <tr class="tr-bg-primary text-nowrap">
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Услуга</th>
                        <th>Ссылка</th>
                        <th>Количество</th>
                        <th>Статус</th>
                        <th>Drip-feed</th>
                        <th>Создан</th>
                        <th class="actions"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->login ?? '—' }}</td>
                            <td>{{ $order->service->name ?? '—' }}</td>
                            <td><a href="{{ $order->link }}" target="_blank" rel="noreferrer">{{ Str::limit($order->link, 40) }}</a></td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->is_drip_feed ? 'Да' : 'Нет' }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.read', $order) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Заказы не найдены.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($orders) }}
        </div>
    </div>
@endsection
