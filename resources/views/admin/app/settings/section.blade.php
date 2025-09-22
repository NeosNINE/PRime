@extends('admin.app.settings.layout')

@section('settings_content')

    <form
        action="#"
        method="POST"
        class="settingsSave"
        data-action="{{ route('admin.settings.save', $section['section_key']) }}"
        data-method="PUT"
        data-success-message="Данные успешно сохранены."
    >
        <div class="box-body settings-section page-loading-opacity">
            @if( isset($section['manage']) )
                @foreach( $section['manage'] as $item )

                    {!! settings()->getItemHTML($item) !!}

                @endforeach
            @endif
        </div>
        @if( settings()->checkAccess($section['section_key'], 'edit') )
            <div class="box-footer">
                <button class="btn btn-primary">Сохранить</button>
            </div>
        @endif
    </form>

    <load-js src="{{ mix('assets/admin/js/pages/settings.js') }}"></load-js>

@endsection
