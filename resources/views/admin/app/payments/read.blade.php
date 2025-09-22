@extends('admin.app.layout')

@section('content')
    <div class="container">

        @include('admin.components.link-back')
        <div class="page-header">
            <h1>Платеж #{{ $transaction->id }}</h1>
            <div class="actions"></div>
        </div>

        <form
            action="#"
            class="inputs-only-read"
        >
            @include('admin.app.payments.components.form', ['transaction' => $transaction])
        </form>

        @if($transaction->status !== 'completed' || true)
            <div class="box mt-3">
                <div class="box-header"><h3>Действия</h3></div>
                <div class="box-body">
                    @if($transaction->status !== 'completed')
                        <button
                            class="btn btn-success me-3"
                            data-ajax-action
                            data-action="{{ route('admin.payments.accept', ['transaction'=>$transaction->id]) }}"
                            data-method="POST"
                            data-confirm-text="Вы уверены, что хотите принять этот платеж?"
                            data-success-text="Платеж успешно принят."
                            data-id="{{ $transaction->id }}"
                            data-event="payment.accept"
                        >
                            <i class="fa fa-check"></i> Принять платеж
                        </button>
                    @endif
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="refund-reason-{{ $transaction->id }}" class="form-control" placeholder="Причина возврата" style="width: 250px;">
                        <button
                            class="btn btn-danger"
                            data-ajax-action
                            data-action="{{ route('admin.payments.refund', ['transaction'=>$transaction->id]) }}"
                            data-method="POST"
                            data-confirm-text="Вы уверены, что хотите вернуть этот платеж?"
                            data-success-text="Платеж успешно возвращен."
                            data-id="{{ $transaction->id }}"
                            data-event="payment.refund"
                            data-extra-data="reason"
                            data-extra-data-source="#refund-reason-{{ $transaction->id }}"
                        >
                            <i class="fa fa-rotate-left"></i> Возврат
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection


