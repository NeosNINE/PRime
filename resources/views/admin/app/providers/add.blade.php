@extends('admin.app.layout')
@section('title', 'Добавить провайдера' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.providers.add.save') }}"
            data-method="POST"
            data-redirect="reload"
            data-success-message="Провайдер создан"
            data-event="provider.add"
        >
            <div class="page-header">
                <h1>Добавить провайдера</h1>
            </div>

            @include('admin.app.providers.components.form', [
                'provider' => null,
                'availableDrivers' => $availableDrivers,
                'driverLabels' => $driverLabels,
                'is_add' => true,
            ])

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection
