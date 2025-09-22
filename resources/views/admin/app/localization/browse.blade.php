@extends('admin.app.layout')
@section('title', 'Локализация' )

@section('content')

    <div class="left-full-height-nav" data-scrollbar>
        <ul>
            @each('admin.app.localization.browse-left-nav-item', $sections, 'section')
        </ul>
    </div>

    <div class="container page-loading-opacity">

        <div class="page-header">
            <h1>Локализация</h1>
            <div class="actions">
                <a href="{{ route('admin.localization.admin_edit_mode') }}" class="btn btn-primary @if( session('localization_edit_mode') ) hide @endif" target="_blank" onclick="$(this).hide();$(this).next().removeClass('hide')"><span>Режим поиска на сайте</span></a>
                <a href="{{ route('admin.localization.admin_edit_mode') }}?cancel=true" class="btn btn-warning @if( !session('localization_edit_mode') ) hide @endif"><span>Завершить режим поиска на сайте</span></a>
            </div>
        </div>
        <div class="box no-padding">
            @if( !request()->input('key') )
                @include('admin.components.search_form')
            @endif

            @if( $section || $locals )

                <form
                    action="#"
                    method="POST"
                    class="ajax-submit"
                    data-action="{{ route('admin.localization.save') }}"
                    data-method="POST"
                    data-success-message="Локализация успешно сохранена."
                >
                    @if( $section )
                        <div class="box-header">
                            <h6>{{ $section->breadcrumb_name }}</h6>
                        </div>
                    @endif
                    <div class="box-body">

                        @if( count($locals) )

                            <div class="locals-items">

                                @foreach( $locals as $local )

                                    <div class="local-item">

                                        @foreach( config('settings.languages') as $lang_key => $lang )

                                            <div class="input" title="{{ $local->key }} : {{ $local->lang_file }}">
                                                <label>
                                                    <span class="label">{{ $lang['name'] }}</span>
                                                    <textarea type="text" name="locals[{{ $local->id }}][{{ $lang_key }}]" @if( $local['type'] == 'html') data-editor @endif autocomplete="password">{{ langTextOrNull($local->text, $lang_key) }}</textarea>
                                                </label>
                                            </div>

                                        @endforeach

                                    </div>


                                @endforeach

                            </div>

                        @else

                            <div class="text-secondary">У выбранного раздела нет контента для локализации.</div>

                        @endif

                    </div>

                    @if( count($locals) )
                        <div class="box-footer actions-fixed">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    @endif

                </form>

                {{ admin()->paginate($locals) }}

            @else

                <div class="no-rows-found">Выберите раздел слева в меню или воспользуйтесь поиском.</div>

            @endif

        </div>

    </div>

@endsection
