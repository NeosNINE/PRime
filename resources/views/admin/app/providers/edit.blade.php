@extends('admin.app.layout')
@section('title', 'Редактировать провайдера' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.providers.edit.save', $provider) }}"
            data-method="PUT"
            data-redirect="reload"
            data-success-message="Данные обновлены"
            data-event="provider.edit"
        >
            <div class="page-header">
                <h1>Редактировать провайдера</h1>
                <div class="actions">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-ajax-action
                        data-action="{{ route('admin.providers.sync_balance', $provider) }}"
                        data-method="POST"
                        data-success-text="Баланс обновлён"
                        data-event="provider.sync"
                    >
                        <i class="fa fa-arrows-rotate"></i> <span>Синхронизировать баланс</span>
                    </button>
                </div>
            </div>

            @include('admin.app.providers.components.form', [
                'provider' => $provider,
                'availableDrivers' => $availableDrivers,
                'driverLabels' => $driverLabels,
                'is_edit' => true,
            ])

            <div class="page-footer">
                <div class="actions">
                    <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
                </div>
            </div>
        </form>

    </div>

@endsection
