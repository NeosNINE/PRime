@extends('admin.app.layout')

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="{{ $item->exists ? route('admin.promocodes.edit.save', $item) : route('admin.promocodes.add.save') }}"
            method="POST"
            class="ajax-submit"
            data-action="{{ $item->exists ? route('admin.promocodes.edit.save', $item) : route('admin.promocodes.add.save') }}"
            data-method="{{ $item->exists ? 'PUT' : 'POST' }}"
            data-redirect="back"
            data-success-message="{{ $item->exists ? 'Промокод обновлён' : 'Промокод создан' }}"
            data-event="{{ $item->exists ? 'promocode.update' : 'promocode.create' }}"
        >
            @csrf

            <div class="page-header">
                <h1>{{ $item->exists ? 'Редактировать промокод' : 'Создать промокод' }}</h1>
                <div class="actions"></div>
            </div>

            <div class="box">
                <div class="box-body">
                    @include('admin.app.promocodes.components.form', ['item' => $item])
                </div>
            </div>

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> <span>{{ $item->exists ? 'Сохранить' : 'Создать' }}</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection


