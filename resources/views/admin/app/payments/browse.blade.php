@extends('admin.app.layout')
@section('title', 'Платежи' )

@section('content')

    <div class="container">

        <div class="page-header">
            <h1>Платежи</h1>
            <div class="actions">
                <!-- Фильтры -->
                <form method="GET" class="d-inline-flex gap-2 me-3">
                    <select name="method" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Все методы</option>
                        @foreach(array_keys(config('payments.methods', [])) as $m)
                            <option value="{{ $m }}" @selected(request()->input('method') === $m)>{{ strtoupper($m) }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Все статусы</option>
                        <option value="pending" @selected(request()->input('status') === 'pending')>Pending</option>
                        <option value="completed" @selected(request()->input('status') === 'completed')>Completed</option>
                        <option value="failed" @selected(request()->input('status') === 'failed')>Failed</option>
                    </select>
                </form>

                {!!
                    admin()->buttons([
                        [
                            'offcanvas-href' => route('admin.payments.balance.form'),
                            'style' => 'primary',
                            'icon' => 'fa fa-wallet',
                            'text' => 'Изменить баланс',
                            'access' => 'payments.balance'
                        ]
                    ])
                !!}
            </div>
        </div>

        <div class="box no-padding">
            @include('admin.components.search_form')

            <div class="box-table">
                <table class="table table-striped @if( !count($items) ) table-no-rows-found @endif">
                    <thead>
                        <tr class="tr-bg-primary text-nowrap">
                            <th class="id">ID</th>
                            <th>Пользователь</th>
                            <th>Сумма</th>
                            <th>Метод</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count($items) )
                            @foreach($items as $transaction)
                                @include('admin.app.payments.components.table-row', ['transaction' => $transaction])
                            @endforeach
                        @else
                            <tr>
                                <td>Ничего не найдено.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($items) }}

        </div>


    </div>

    <load-js src="{{ mix('assets/admin/js/pages/payments.js') }}"></load-js>

@endsection


