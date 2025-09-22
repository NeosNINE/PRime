@if( request()->ajax() && !request()->input('load-in-iframe') )

    @if( !request()->input('modal_page') )
        <script>
            if( !document.getElementById('content') )
                window.location.reload();

            document.title = "@yield('title')";
            window.changePathName('{{ request()->path() }}');
        </script>
    @endif

    @yield('content')

@else
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title')</title>

    <meta name="csrf" content="{{ csrf_token() }}">
    <meta name="current_route" content="{{ request()->route()?->getName() }}">

    <link rel="icon" type="image/png" href="{{ $favicon ?? asset('favicon.png') }}">

    <link href="{{ mix('/assets/admin/css/libs.css') }}" rel="stylesheet">
    <link href="{{ mix('/assets/admin/css/app.css') }}" rel="stylesheet" data-app-css>

    <link href="{{ admin()->getTheme()['css_path'] }}" rel="stylesheet" data-them-css="{{ admin()->getTheme()['key'] }}">

</head>
<body class="app-loading" data-bs-theme="{{ admin()->getTheme()['key'] }}">

    <div
        id="app"
        @class([
            'is-over-top-nav' => admin()->isNavRegistered('overTop'),
            'is-sidebar-nav' => admin()->isNavRegistered('left'),
            'loaded-as-iframe' => request()->input('load-in-iframe')
        ])
    >

        @if( admin()->isNavRegistered('left') )
            <section id="sidebar-nav">
                <a class="logo" href="{{ route('index') }}">@include('admin.components.logo')</a>
                <nav class="nav">
                    <ul>
                        @if( admin()->isNavRegistered('left') )
                            @include('admin.components.navigation.left')
                        @endif
                    </ul>
                </nav>
            </section>
        @endif

        <section id="page-container">

            <section id="header">

                @if( admin()->isNavRegistered('overTop') )

                    <div class="over-top-nav nav">
                        <div class="container d-flex">
                            <ul>
                                @include('admin.components.navigation.over_top')
                            </ul>
                        </div>
                    </div>

                @endif

                <div class="container d-flex">
                    <a class="logo" href="{{ route('index') }}">@include('admin.components.logo')</a>

                    <nav class="nav">
                        <ul>
                            @if( admin()->isNavRegistered('top') )
                                @include('admin.components.navigation.top')
                            @endif
                        </ul>
                    </nav>

                    <div class="nav-right nav">
                        <a href="#" class="avatar-link open-sub-nav">
                            <div class="img-avatar" style="background-image:url('{{ auth()->user()->avatar }}')"></div>
                            <i class="fa fa-chevron-down"></i>
                        </a>
                        <div id="avatar-sub-nav" class="sub-nav">
                            {{--<div class="sub-nav-info">{{ auth()->user()->email }}</div>--}}
                            <ul>
                                @if( admin()->isNavRegistered('topProfile') )
                                    @include('admin.components.navigation.top_profile')
                                @endif
                            </ul>
                        </div>
                    </div>

                </div>
            </section>

            <section id="content">
                @yield('content')
            </section>

        </section>


    </div>


    <script type="text/javascript">

        window.last_client_event_id = {{ events()->getLastClientId() }};
        window.routes = {!! getRoutesListByName() !!};
        window.acceses = {!! roles()->getUserAccessesKeys(toJson: true) !!};
        window.themes = @json(admin()->getAvailableThemes());
        window.theme_can_change = {{ admin()->can_change_theme }};
        window.section_type = 'admin';

    </script>
    <script type="text/javascript" src="{{ mix('/assets/admin/js/libs.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/assets/admin/js/app.js') }}"></script>

    @production

        @if( auth()->check() && auth()->user()->isDeveloper() )
            <script type="text/javascript">
                swalWarning('Вы зашли в административную часть на PRODUCTION.');
            </script>
        @endif

    @endproduction

</body>
</html>
@endif
