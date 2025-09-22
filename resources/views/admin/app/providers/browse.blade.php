@extends('admin.app.layout')
@section('title', 'Провайдеры услуг' )

@section('content')

    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="container">

        <div class="page-header">
            <h1>Провайдеры услуг</h1>
            <div class="actions">
                @if( roles()->checkAccess('providers.add') )
                    {!!
                        admin()->buttons([
                            [
                                'offcanvas-href' => route('admin.providers.add'),
                                'style' => 'primary',
                                'icon' => 'fa fa-plus',
                                'text' => 'Добавить провайдера',
                                'access' => 'providers.add'
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
                        <select name="driver" class="form-select select2" onchange="this.form.submit()">
                            <option value="">Все драйверы</option>
                            @foreach($availableDrivers as $driver)
                                <option value="{{ $driver }}" @selected($filters['driver'] === $driver)>{{ $driverLabels[$driver] ?? Str::title($driver) }}</option>
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

            <div class="box-table">
                <table class="table table-striped providers-table @if( !count($providers) ) table-no-rows-found @endif">
                    <thead>
                        <tr class="tr-bg-primary text-nowrap">
                            <th class="id">ID</th>
                            <th>Провайдер</th>
                            <th>Баланс</th>
                            <th>Последняя синхронизация</th>
                            <th>Статус</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if( count($providers) )
                        @foreach( $providers as $provider )
                            @include('admin.app.providers.components.table-row', ['provider' => $provider, 'driverLabels' => $driverLabels])
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">Ничего не найдено.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($providers) }}
        </div>

    </div>

@endsection
