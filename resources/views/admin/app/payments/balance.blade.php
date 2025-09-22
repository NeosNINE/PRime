@extends('admin.app.layout')
@section('title', 'Изменить баланс')

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="{{ route('admin.payments.balance') }}"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.payments.balance') }}"
            data-method="POST"
            data-redirect="back"
            data-success-message="Баланс успешно изменен"
            data-event="payment.balance"
        >
            <div class="page-header">
                <h1>Изменить баланс</h1>
                <div class="actions">
                </div>
            </div>

            <div class="box">
                <div class="box-body">
                    <div class="input">
                        <label>
                            <span class="label">Пользователь <i>*</i></span>
                            <select name="user_id"
                                    class="select2"
                                    data-search-model="user"
                                    data-search-columns="id login email"
                                    data-search-field="id"
                                    data-search-template-result="#item.id — item.login (item.email)"
                                    data-search-template-selected="item.login (ID: item.id)"
                                    required>
                                @if(request()->filled('user_id'))
                                    @php($u = \App\Models\User::find(request()->input('user_id')))
                                    @if($u)
                                        <option value="{{ $u->id }}" selected>{{ $u->login ?? $u->email }} (ID: {{ $u->id }})</option>
                                    @endif
                                @endif
                            </select>
                        </label>
                        <span class="tooltip-btn" data-bs-title="Начните вводить email или логин, затем выберите пользователя"></span>
                    </div>

                    <div class="input">
                        <label>
                            <span class="label">Сумма <i>*</i></span>
                            <input type="number" step="0.01" name="amount" value="{{ request()->input('amount') }}" placeholder="Положительное или отрицательное число" required>
                        </label>
                        <span class="tooltip-btn" data-bs-title="Положительное число - пополнение, отрицательное - списание"></span>
                    </div>

                    <div class="input">
                        <label>
                            <span class="label">Причина</span>
                            <input type="text" name="reason" value="{{ request()->input('reason') }}" placeholder="Причина изменения баланса">
                        </label>
                    </div>
                </div>
            </div>

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-wallet"></i> <span>Изменить баланс</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection
