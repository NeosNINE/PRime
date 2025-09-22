@extends('admin.app.layout')
@section('title', 'Создать промокод')

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="{{ route('admin.promocodes.add.save') }}"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.promocodes.add.save') }}"
            data-method="POST"
            data-redirect="back"
            data-success-message="Промокод создан"
            data-event="promocode.add"
        >
            @csrf

            <div class="page-header">
                <h1>Создать промокод</h1>
                <div class="actions"></div>
            </div>

            <div class="box">
                <div class="box-body">
                    @include('admin.app.promocodes.components.form', ['item' => new \App\Models\System\PromoCode()])
                </div>
            </div>

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-plus"></i> <span>Создать</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection


