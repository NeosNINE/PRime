@extends('admin.app.layout')
@section('title', 'Пользователь #'.$user->id )

@section('content')

    <div class="container">

        @include('admin.components.link-back')
        <div class="page-header">
            <h1>Пользователь #{{ $user->id }}</h1>
            <div class="actions">

            </div>
        </div>

        <form
            action="#"
            class="inputs-only-read"
        >

            @include('admin.app.users.components.form', ['is_read' => true] )

        </form>

        <div class="page-footer">
            <div class="actions">
                @if( !$user->isAnotherSuperAdmin() )
                    @if( roles()->checkAccess('users.edit') )
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="fa fa-pen"></i> <span>Редактировать</span></a>
                    @endif
                    @if( roles()->checkAccess('users.delete') )
                        <button
                            class="btn btn-danger"
                            data-delete-object
                            data-action="{{ route('admin.users.delete', $user) }}"
                            data-confirm-text="Вы уверены, что желаете удалить пользователя?"
                            data-success-text="Пользователь успешно удален."
                            data-id="{{ $user->id }}"
                            data-event="user.delete"
                        >
                            <i class="fa fa-trash-alt"></i> <span>Удалить</span>
                        </button>
                    @endif
                @else
                    <div class="text-muted">Другой Super Admin не может быть отредактирован или удален.</div>
                @endif
            </div>
        </div>

    </div>


@endsection
