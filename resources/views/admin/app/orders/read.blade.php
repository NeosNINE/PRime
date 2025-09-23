@extends('admin.app.layout')
@section('title', 'Заказ #' . $order->id )

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Заказ #{{ $order->id }}</h1>
        </div>

        <div class="box">
            <div class="box-body">
                <dl class="row">
                    <dt class="col-sm-3">Пользователь</dt>
                    <dd class="col-sm-9">{{ $order->user->login ?? '—' }} (ID: {{ $order->user_id }})</dd>

                    <dt class="col-sm-3">Услуга</dt>
                    <dd class="col-sm-9">{{ $order->service->name ?? '—' }}</dd>

                    <dt class="col-sm-3">Провайдер</dt>
                    <dd class="col-sm-9">{{ $order->provider->name ?? 'Ручная обработка' }}</dd>

                    <dt class="col-sm-3">Ссылка</dt>
                    <dd class="col-sm-9"><a href="{{ $order->link }}" target="_blank" rel="noreferrer">{{ $order->link }}</a></dd>

                    <dt class="col-sm-3">Количество</dt>
                    <dd class="col-sm-9">{{ $order->quantity }}</dd>

                    <dt class="col-sm-3">Стоимость / Себестоимость</dt>
                    <dd class="col-sm-9">{{ number_format($order->price, 4, '.', ' ') }} $ / {{ number_format($order->cost_price, 4, '.', ' ') }} $</dd>

                    <dt class="col-sm-3">Статус</dt>
                    <dd class="col-sm-9">{{ ucfirst($order->status) }}</dd>

                    <dt class="col-sm-3">Drip-feed</dt>
                    <dd class="col-sm-9">{{ $order->is_drip_feed ? 'Да' : 'Нет' }}</dd>

                    <dt class="col-sm-3">Создан</dt>
                    <dd class="col-sm-9">{{ $order->created_at }}</dd>

                    <dt class="col-sm-3">Обновлен</dt>
                    <dd class="col-sm-9">{{ $order->updated_at }}</dd>
                </dl>
            </div>
        </div>

        @if($order->is_manual && roles()->checkAccess('orders.edit'))
            <div class="box">
                <div class="box-header">
                    <h3>Изменение статуса</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.orders.update_status', $order) }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-md-4">
                            <label class="form-label">Статус</label>
                            <select name="status" class="form-select">
                                @foreach(['pending', 'in_progress', 'completed', 'failed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Комментарий</label>
                            <input type="text" name="comment" class="form-control" value="{{ old('comment', data_get($order->meta, 'admin_comment')) }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Обновить</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($order->runs->count())
            <div class="box">
                <div class="box-header">
                    <h3>Запуски Drip-feed</h3>
                </div>
                <div class="box-table">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Количество</th>
                            <th>Статус</th>
                            <th>Запланирован</th>
                            <th>Отправлен</th>
                            <th>Завершен</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->runs as $run)
                            <tr>
                                <td>{{ $run->run_number }}</td>
                                <td>{{ $run->quantity }}</td>
                                <td>{{ ucfirst($run->status) }}</td>
                                <td>{{ $run->scheduled_for }}</td>
                                <td>{{ $run->dispatched_at }}</td>
                                <td>{{ $run->completed_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
