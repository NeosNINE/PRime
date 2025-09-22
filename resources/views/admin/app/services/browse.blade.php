@extends('admin.app.layout')
@section('title', 'Услуги' )

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Услуги</h1>
            <div class="actions">
                @if(roles()->checkAccess('services.add'))
                    {!!
                        admin()->buttons([
                            [
                                'offcanvas-href' => route('admin.services.add'),
                                'style' => 'primary',
                                'icon' => 'fa fa-plus',
                                'text' => 'Добавить услугу',
                                'access' => 'services.add'
                            ]
                        ])
                    !!}
                @endif
            </div>
        </div>

        <div class="box no-padding">
            <div class="box-filters">
                <div class="filters-grid">
                    <form class="form-search" method="GET" action="">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" placeholder="Поиск" value="{{ $filters['search'] }}">
                    </form>
                    <form class="filters-inline" method="GET" action="">
                        <input type="hidden" name="search" value="{{ $filters['search'] }}">
                        <select name="provider_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="">Все провайдеры</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" @selected($filters['provider_id'] == $provider->id)>{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        <select name="category_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($filters['category_id'] == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select select2" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="active" @selected($filters['status'] === 'active')>Активные</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Отключенные</option>
                        </select>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.services.bulk') }}">
                @csrf
                <div class="box-table">
                    <table class="table table-striped services-table @if(!$services->count()) table-no-rows-found @endif">
                        <thead>
                        <tr class="tr-bg-primary text-nowrap">
                            <th><input type="checkbox" id="services-check-all"></th>
                            <th>ID</th>
                            <th>Услуга</th>
                            <th>Категория</th>
                            <th>Провайдер</th>
                            <th>Закупочная цена</th>
                            <th>Цена для клиента</th>
                            <th>Мин./Макс.</th>
                            <th>Заказов</th>
                            <th>Статус</th>
                            <th class="actions"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($services as $service)
                            @include('admin.app.services.components.table-row', ['service' => $service])
                        @empty
                            <tr>
                                <td colspan="11">Ничего не найдено.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="box-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="bulk-actions">
                        <select name="action" class="form-select">
                            <option value="enable">Включить</option>
                            <option value="disable">Выключить</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">Применить к выбранным</button>
                    </div>
                    {{ admin()->paginate($services) }}
                </div>
            </form>
        </div>

        <div class="box">
            <div class="box-header">
                <h3>Управление наценками</h3>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('admin.services.markups.save') }}" class="mb-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Область применения</label>
                            <select name="scope" class="form-select" required>
                                <option value="global">Все услуги</option>
                                <option value="provider">Провайдер</option>
                                <option value="category">Категория</option>
                                <option value="service">Услуга</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Провайдер</label>
                            <select name="provider_id" class="form-select select2">
                                <option value="">---</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Категория</label>
                            <select name="service_category_id" class="form-select select2">
                                <option value="">---</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Услуга</label>
                            <select name="service_id" class="form-select select2">
                                <option value="">---</option>
                                @foreach($services as $serviceItem)
                                    <option value="{{ $serviceItem->id }}">{{ $serviceItem->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Наценка, %</label>
                            <input type="number" step="0.01" name="percent" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Наценка, фикс.</label>
                            <input type="number" step="0.01" name="fixed" class="form-control">
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary">Сохранить наценку</button>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Область</th>
                        <th>Описание</th>
                        <th>Наценка</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($markups as $markup)
                        <tr>
                            <td>{{ strtoupper($markup->scope) }}</td>
                            <td>
                                @if($markup->scope === 'provider')
                                    Провайдер: {{ $markup->provider->name ?? '—' }}
                                @elseif($markup->scope === 'category')
                                    Категория: {{ $markup->category->name ?? '—' }}
                                @elseif($markup->scope === 'service')
                                    Услуга: {{ $markup->service->name ?? '—' }}
                                @else
                                    Все услуги
                                @endif
                            </td>
                            <td>
                                {{ $markup->percent ? $markup->percent . '%' : '' }}
                                @if($markup->fixed)
                                    {{ $markup->percent ? '+' : '' }}{{ number_format($markup->fixed, 2, '.', ' ') }} $
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.services.markups.delete', $markup) }}" onsubmit="return confirm('Удалить наценку?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Наценки не настроены.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('services-check-all')?.addEventListener('change', function (event) {
            document.querySelectorAll('.service-checkbox').forEach(function (checkbox) {
                checkbox.checked = event.target.checked;
            });
        });
    </script>
@endpush
