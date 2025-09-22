@extends('admin.app.layout')
@section('title', 'Редактировать промокод #'.$item->id )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.promocodes.edit.save', ['promocode' => $item->id]) }}"
            data-method="PUT"
            data-redirect="back_with_update"
            data-success-message="Промокод успешно сохранен"
            data-event="promocode.edit"
        >
        <div class="page-header">
            <h1><a href="{{ route('admin.promocodes.read', $item) }}">Промокод #{{ $item->id }}</a></h1>
            <div class="actions">
            </div>
        </div>

        <div class="box">
            <div class="box-body">
                @include('admin.app.promocodes.components.form', ['item' => $item] )
            </div>
        </div>

        <div class="page-footer">
            <div class="actions">
                <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
            </div>
        </div>
    </form>

    </div>

@endsection


