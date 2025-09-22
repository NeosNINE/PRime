@extends('admin.app.layout')
@section('title', 'Промокоды')
@section('content')

    <div class="container">

        <div class="page-header">
            <h1 class="page-title">Промокоды</h1>
            <div class="actions">
                {!! admin()->buttons([
                    [
                        'offcanvas-href' => route('admin.promocodes.add'),
                        'style' => 'primary',
                        'icon' => 'fa fa-plus',
                        'text' => 'Создать промокод',
                        'access' => 'promo_codes.add',
                    ],
                ]) !!}
            </div>
        </div>

        <div class="mt-20">
            <form method="get" class="filters d-flex gap-2">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Тип: все</option>
                    <option value="general" @if (request('type') === 'general') selected @endif>Общий</option>
                    <option value="individual" @if (request('type') === 'individual') selected @endif>Индивидуальный</option>
                </select>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Статус: все</option>
                    <option value="active" @if (request('status') === 'active') selected @endif>Активен</option>
                    <option value="inactive" @if (request('status') === 'inactive') selected @endif>Неактивен</option>
                </select>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="created_at" @if (request('sort_by') === 'created_at') selected @endif>Дата создания</option>
                    <option value="bonus_amount" @if (request('sort_by') === 'bonus_amount') selected @endif>Сумма бонуса</option>
                </select>
                <select name="sort_order" class="form-select" onchange="this.form.submit()">
                    <option value="desc" @if (request('sort_order') === 'desc') selected @endif>По убыванию</option>
                    <option value="asc" @if (request('sort_order') === 'asc') selected @endif>По возрастанию</option>
                </select>
            </form>
        </div>

        <div class="box box-table">
            <table class="table table-striped promocodes-table @if (!$items->count()) table-no-rows-found @endif"
                data-tr-type="promocodes" data-table-row-template="admin.app.promocodes.components.table-row">
                <thead>
                    <tr class="tr-bg-primary text-nowrap">
                        <th>Код</th>
                        <th>Тип</th>
                        <th>Бонус</th>
                        <th>Срок действия</th>
                        <th>Пользователи</th>
                        <th>Статус</th>
                        <th class="actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @include('admin.app.promocodes.components.table-row', ['item' => $item])
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Промокоды не найдены</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $items->links() }}
        </div>

    </div>
@endsection


@push('js')
    <load-js src="{{ mix('assets/admin/js/pages/promocodes.js') }}"></load-js>
@endpush
