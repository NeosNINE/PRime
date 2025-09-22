@extends('admin.app.layout')
@section('title', 'Редактировать роль '.$role->name )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <div class="page-header">
            <h1>Редактировать роль</h1>
            <div class="actions">
            </div>
        </div>

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.roles.edit.save', $role) }}"
            data-method="PUT"
            data-redirect="back_with_update"
            data-success-message="Роль успешно изменена."
            data-event="role.edit"
        >


            @include('admin.app.roles.components.form')

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
                </div>
            </div>

        </form>

    </div>


    <load-css src="{{ mix('assets/admin/css/pages/roles.css') }}"></load-css>
    <load-js src="{{ mix('assets/admin/js/pages/roles.js') }}"></load-js>

@endsection
