@extends('admin.app.layout')
@section('title', 'Редактировать пользователя #'.$user->id )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.users.edit.save', $user) }}"
            data-method="PUT"
            data-redirect="back_with_update"
            data-success-message="Пользователь успешно сохранен"
            data-event="user.edit"
        >
        <div class="page-header">
            <h1><a href="{{ route('admin.users.read', $user) }}">Пользователь #{{ $user->id }}</a></h1>
            <div class="actions">
            </div>
        </div>

        @include('admin.app.users.components.form', ['is_edit' => true] )

        <div class="page-footer">
            <div class="actions">
                <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
            </div>
        </div>
    </form>

    </div>

@endsection
