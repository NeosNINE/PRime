<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ request()->cookie('theme', 'light') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SOCNET SMM - Лучшая SMM панель')</title>
    <meta name="description" content="@yield('description', 'SOCNET SMM — надёжная и сверхбыстрая платформа для продвижения в социальных сетях по доступным ценам. Увеличивайте охваты, покоряйте новую аудиторию.')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Apply saved theme ASAP to avoid flash -->
    <script>
        (function(){
            try {
                var m=document.cookie.match(/(?:^|; )theme=([^;]+)/);
                var t=(localStorage.getItem('theme')||(m&&decodeURIComponent(m[1])));
                if(!t && window.matchMedia){ t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'; }
                if(t){ document.documentElement.setAttribute('data-theme', t); }
            } catch(e) {}
        })();
    </script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('assets/guest/css/libs.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/guest/css/app.css') }}">
    @guest
        <link rel="stylesheet" href="{{ mix('assets/guest/css/components/header.css') }}">
    @endguest
    @auth
        <link rel="stylesheet" href="{{ mix('assets/user/css/components/header.css') }}">
    @endauth

    <!-- Additional styles -->
    @yield('styles')
</head>

<body>

    @include('guest.app.components.header')

    @auth
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
    @endauth

    <!-- Main Content -->
    <main class="main" id="main">
        @yield('content')
    </main>

    @include('guest.app.components.footer')


    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="{{ __('Наверх') }}">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Auth Modals -->
    @guest
        @include('guest.app.components.auth-modals')
    @endguest

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ mix('assets/guest/js/libs.js') }}"></script>
    <script type="text/javascript" src="{{ mix('assets/guest/js/app.js') }}"></script>
    @guest
        <script type="text/javascript" src="{{ mix('assets/guest/js/components/header.js') }}"></script>
    @endguest
    @auth
        <script type="text/javascript" src="{{ mix('assets/user/js/components/header.js') }}"></script>
    @endauth

    <!-- Additional scripts -->
    @yield('scripts')
</body>

</html>
