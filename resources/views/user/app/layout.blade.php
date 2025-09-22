<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ request()->cookie('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <meta name="csrf" content="{{ csrf_token() }}">
    <meta name="current_route" content="{{ request()->route()?->getName() }}">

    <!-- Apply saved theme ASAP to avoid flash -->
    <script>
        (function() {
            try {
                var m = document.cookie.match(/(?:^|; )theme=([^;]+)/);
                var t = (localStorage.getItem('theme') || (m && decodeURIComponent(m[1])));
                if (!t && window.matchMedia) {
                    t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                if (t) {
                    document.documentElement.setAttribute('data-theme', t);
                }
            } catch (e) {}
        })();
    </script>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="stylesheet" href="{{ mix('assets/user/css/libs.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/user/css/app.css') }}">

</head>

<body>

    @include('user.components.header')

    <div class="wrapper">
        <div class="container">
            <div class="content-wrap">
                @include('user.components.sidebar')
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Background Icons -->
        <div class="wrapper-bg-icons">
            <div class="wrapper-bg-icon wrapper-icon-1"><i class="fab fa-instagram"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-2"><i class="fab fa-youtube"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-3"><i class="fab fa-tiktok"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-4"><i class="fab fa-telegram"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-5"><i class="fab fa-facebook"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-6"><i class="fab fa-twitter"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-7"><i class="fas fa-rocket"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-8"><i class="fas fa-chart-line"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-9"><i class="fas fa-users"></i></div>
            <div class="wrapper-bg-icon wrapper-icon-10"><i class="fas fa-heart"></i></div>
        </div>
    </div>

    @include('guest.app.components.footer')

    @yield('footer')

    <div class="modal fade model-logout" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="logout-confirm-text">{{ __('Вы точно хотите выйти?') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-logout-yes" id="confirmLogoutBtn">{{ __('Да') }}</button>
                    <button type="button" class="btn-logout-no" data-bs-dismiss="modal">{{ __('Нет') }}</button>
                    <form class="form-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ mix('assets/user/js/libs.js') }}"></script>
    <script type="text/javascript" src="{{ mix('assets/user/js/app.js') }}"></script>

    @stack('js')

</body>

</html>
