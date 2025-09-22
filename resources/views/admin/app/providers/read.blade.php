@extends('admin.app.layout')
@section('title', 'Провайдер #'.$provider->id )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <div class="page-header">
            <h1>Провайдер #{{ $provider->id }}</h1>
            <div class="actions">
                @if( roles()->checkAccess('providers.edit') )
                    <button
                        type="button"
                        class="btn btn-default"
                        data-ajax-action
                        data-action="{{ route('admin.providers.sync_balance', $provider) }}"
                        data-method="POST"
                        data-success-text="Баланс обновлён"
                        data-event="provider.sync"
                    >
                        <i class="fa fa-arrows-rotate"></i> <span>Обновить баланс</span>
                    </button>
                @endif
            </div>
        </div>

        <form action="#" class="inputs-only-read">
            @include('admin.app.providers.components.form', [
                'provider' => $provider,
                'availableDrivers' => $availableDrivers,
                'driverLabels' => $driverLabels,
                'is_read' => true,
            ])
        </form>

        <div class="page-footer">
            <div class="actions">
                @if( roles()->checkAccess('providers.edit') )
                    <a href="{{ route('admin.providers.edit', $provider) }}" class="btn btn-primary"><i class="fa fa-pen"></i> <span>Редактировать</span></a>
                    @if($provider->is_active)
                        <button
                            class="btn btn-default"
                            data-ajax-action
                            data-action="{{ route('admin.providers.deactivate', $provider) }}"
                            data-method="POST"
                            data-confirm-text="Отключить провайдера?"
                            data-success-text="Провайдер отключён"
                            data-event="provider.deactivate"
                        >
                            <i class="fa fa-toggle-on"></i> <span>Отключить</span>
                        </button>
                    @else
                        <button
                            class="btn btn-success"
                            data-ajax-action
                            data-action="{{ route('admin.providers.activate', $provider) }}"
                            data-method="POST"
                            data-success-text="Провайдер активирован"
                            data-event="provider.activate"
                        >
                            <i class="fa fa-toggle-off"></i> <span>Активировать</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>

    </div>

@endsection
