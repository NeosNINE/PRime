@extends('admin.app.layout')
@section('title', 'Просмотр услуги' )

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Услуга #{{ $service->id }}</h1>
            <div class="actions">
                {!! admin()->buttons([
                    [
                        'href' => route('admin.services.edit', $service),
                        'style' => 'secondary',
                        'icon' => 'fa fa-pencil',
                        'text' => 'Редактировать',
                        'access' => 'services.edit'
                    ]
                ]) !!}
            </div>
        </div>

        <div class="box">
            <div class="box-body">
                <dl class="row">
                    <dt class="col-sm-3">Название</dt>
                    <dd class="col-sm-9">{{ $service->name }}</dd>

                    <dt class="col-sm-3">Категория</dt>
                    <dd class="col-sm-9">{{ $service->category->name ?? '—' }}</dd>

                    <dt class="col-sm-3">Провайдер</dt>
                    <dd class="col-sm-9">{{ $service->provider->name ?? 'Ручная услуга' }}</dd>

                    <dt class="col-sm-3">Минимальное количество</dt>
                    <dd class="col-sm-9">{{ $service->min_quantity }}</dd>

                    <dt class="col-sm-3">Максимальное количество</dt>
                    <dd class="col-sm-9">{{ $service->max_quantity }}</dd>

                    <dt class="col-sm-3">Закупочная цена</dt>
                    <dd class="col-sm-9">{{ number_format($service->cost_price, 4, '.', ' ') }} $</dd>

                    <dt class="col-sm-3">Цена для клиента</dt>
                    <dd class="col-sm-9">{{ number_format($service->price, 4, '.', ' ') }} $</dd>

                    <dt class="col-sm-3">Статус</dt>
                    <dd class="col-sm-9">{{ $service->is_active ? 'Активна' : 'Отключена' }}</dd>

                    <dt class="col-sm-3">Описание</dt>
                    <dd class="col-sm-9">{!! nl2br(e($service->description)) !!}</dd>

                    <dt class="col-sm-3">Создана</dt>
                    <dd class="col-sm-9">{{ $service->created_at }}</dd>

                    <dt class="col-sm-3">Обновлена</dt>
                    <dd class="col-sm-9">{{ $service->updated_at }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
