@extends('admin.app.layout')
@section('title', 'Логи')

@section('content')

    <div class="container">

        <div class="page-header">
            <h1>Логи</h1>
            <div class="actions">
                <form method="GET" class="d-inline-flex gap-2">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Все типы</option>
                        <option value="login" @selected(request('type')==='login')>Вход</option>
                        <option value="balance_increase" @selected(request('type')==='balance_increase')>Выдача баланса</option>
                        <option value="balance_decrease" @selected(request('type')==='balance_decrease')>Списание баланса</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="box no-padding">
            <div class="box-table">
                <table class="table table-striped @if( !$items->count() ) table-no-rows-found @endif">
                    <thead>
                        <tr class="tr-bg-primary text-nowrap">
                            <th>Дата</th>
                            <th>Пользователь</th>
                            <th>Тип</th>
                            <th>Описание</th>
                            <th>Детали</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $log)
                            <tr>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ optional($log->causer)->login ?? optional($log->causer)->email ?? '—' }}</td>
                                <td>
                                    @php(
                                        $typeMap = [
                                            'login' => ['label' => 'Вход в админку', 'class' => 'label-default'],
                                            'balance_increase' => ['label' => 'Пополнение баланса', 'class' => 'label-success'],
                                            'balance_decrease' => ['label' => 'Списание баланса', 'class' => 'label-danger'],
                                        ]
                                    )
                                    @php($t = $typeMap[$log->log_name] ?? ['label' => $log->log_name, 'class' => 'label-default'])
                                    <span class="d-label {{ $t['class'] }}">{{ $t['label'] }}</span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td class="text-nowrap">
                                    @if( $log->log_name === 'balance_increase' || $log->log_name === 'balance_decrease')
                                        @php($amount = (float) $log->getExtraProperty('amount_usd'))
                                        @php($bonus = $log->getExtraProperty('bonus_usd'))
                                        @php($prev = $log->getExtraProperty('previous_balance'))
                                        @php($next = $log->getExtraProperty('new_balance'))
                                        @php($method = $log->getExtraProperty('method'))
                                        @php($reason = $log->getExtraProperty('reason'))
                                        @php($txId = $log->getExtraProperty('transaction_id'))

                                        <span class="d-label">Сумма: ${{ number_format($amount, 2) }}</span>
                                        @if( !is_null($bonus) )
                                            <span class="d-label label-primary">Бонус: ${{ number_format((float)$bonus, 2) }}</span>
                                        @endif
                                        @if( !is_null($prev) && !is_null($next) )
                                            <span class="d-label label-default">Баланс: ${{ number_format((float)$prev,2) }} → ${{ number_format((float)$next,2) }}</span>
                                        @endif
                                        @if( !empty($method) )
                                            <span class="d-label label-default">Метод: {{ strtoupper($method) }}</span>
                                        @endif
                                        @if( !empty($reason) )
                                            <span class="d-label label-warning">Причина: {{ $reason }}</span>
                                        @endif
                                        @if( !empty($txId) )
                                            <a data-offcanvas-href="{{ route('admin.payments.read', (int)$txId) }}" class="btn btn-xs btn-outline">Транзакция #{{ (int)$txId }}</a>
                                        @endif
                                    @elseif( $log->log_name === 'login')
                                        @php($ip = $log->getExtraProperty('ip'))
                                        @if( !empty($ip) )
                                            <span class="d-label label-default">IP: {{ $ip }}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td>Ничего не найдено.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($items) }}

        </div>

    </div>

@endsection


