@extends('admin.app.layout')
@section('title', 'Добавить роль' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')
        <div class="page-header">
            <h1>Добавить роль</h1>
            <div class="actions">
            </div>
        </div>
        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.roles.add.save') }}"
            data-method="POST"
            data-redirect="back"
            data-success-message="Роль успешно добавлена."
            data-event="role.add"
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
