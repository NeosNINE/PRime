@extends('admin.app.layout')
@section('title', 'Добавить пользователя' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.users.add.save') }}"
            data-method="POST"
            data-redirect="back"
            data-success-message="Пользователь успешно добавлен"
            data-event="user.add"
        >
            <div class="page-header">
                <h1>Добавить пользователя</h1>
                <div class="actions">
                </div>
            </div>

            @include('admin.app.users.components.form', ['is_add' => true] )

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection
