@guest
    <header class="header" id="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <div class="navbar-brand">
                    <a href="{{ route('index') }}" class="logo">
                        <img src="{{ asset('assets/logo.svg') }}" alt="SOCNET SMM" class="logo-icon">
                    </a>
                </div>

                <!-- Navigation Menu (hidden on mobile by default) -->
                <div class="navbar-menu" id="navbar-menu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('services') }}" class="nav-link">{{ __('Услуги') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('news.index') }}" class="nav-link">{{ __('Новости') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('rules') }}" class="nav-link">{{ __('Правила') }}</a>
                        </li>
                    </ul>

                    <!-- Mobile additional controls -->
                    <div class="mobile-additional-controls">

                        <!-- Auth Buttons -->
                        <div class="auth-buttons">
                            <button class="btn btn-outline" data-auth-open="login">{{ __('Вход') }}</button>
                            <button class="btn btn-primary" data-auth-open="register">{{ __('Регистрация') }}</button>
                        </div>

                    </div>
                </div>

                <!-- Desktop controls -->
                <div class="navbar-controls">
                    <!-- Theme Toggle -->
                    <button class="control-btn theme-toggle" data-toggle="theme" title="{{ __('Переключить тему') }}">
                        <span class="theme-icon theme-icon-light"><i class="fas fa-moon"></i></span>
                        <span class="theme-icon theme-icon-dark"><i class="fas fa-sun"></i></span>
                    </button>

                    <!-- Language Selector -->
                    <div class="control-dropdown language-selector">
                        <button class="control-btn" data-toggle="dropdown">
                            <img src="{{ asset('assets/flags/' . app()->getLocale() . '.svg') }}"
                                alt="{{ app()->getLocale() }}" class="flag-icon">
                            <span>{{ strtoupper(app()->getLocale()) === 'RU' ? 'РУ' : strtoupper(app()->getLocale()) }}</span>
                            <i class="fas fa-chevron-down language-arrow"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-lang="ru">
                                    <img src="{{ asset('assets/flags/ru.svg') }}" alt="RU" class="flag-icon">
                                    <span>РУ</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" data-lang="en">
                                    <img src="{{ asset('assets/flags/en.svg') }}" alt="EN" class="flag-icon">
                                    <span>EN</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" data-lang="cn">
                                    <img src="{{ asset('assets/flags/cn.svg') }}" alt="CN" class="flag-icon">
                                    <span>中文</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="auth-buttons">
                        <button class="btn btn-outline" data-auth-open="login">{{ __('Вход') }}</button>
                        <button class="btn btn-primary" data-auth-open="register">{{ __('Регистрация') }}</button>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button class="navbar-toggle" type="button" data-toggle="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>
@endguest

@auth
    <header class="header" id="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <div class="navbar-brand">
                    <a href="{{ route('index') }}" class="logo">
                        <img src="{{ asset('assets/logo.svg') }}" alt="SOCNET SMM" class="logo-icon">
                    </a>
                </div>

                <!-- Desktop controls -->
                <div class="navbar-controls">
                    <!-- Balance Selector -->
                    <div class="balance-selector">
                        <button class="balance-btn" data-toggle="dropdown">
                            <span class="balance-icon" data-currency="usd">$</span>
                            <span class="balance-amount" data-currency="usd">1,250.00</span>
                            <i class="fas fa-chevron-down balance-arrow"></i>
                        </button>
                        <div class="dropdown-menu balance-dropdown-menu">
                            <ul class="balance-list">
                                <li><a href="#" class="balance-option active" data-currency="usd">
                                        <span class="currency-icon">$</span>
                                        <span class="currency-name">USD</span>
                                        <span class="currency-amount">1,250.00</span>
                                    </a></li>
                                <li><a href="#" class="balance-option" data-currency="eur">
                                        <span class="currency-icon">€</span>
                                        <span class="currency-name">EUR</span>
                                        <span class="currency-amount">1,150.75</span>
                                    </a></li>
                                <li><a href="#" class="balance-option" data-currency="rub">
                                        <span class="currency-icon">₽</span>
                                        <span class="currency-name">RUB</span>
                                        <span class="currency-amount">95,200.50</span>
                                    </a></li>
                                <li><a href="#" class="balance-option" data-currency="cny">
                                        <span class="currency-icon">¥</span>
                                        <span class="currency-name">CNY</span>
                                        <span class="currency-amount">8,950.25</span>
                                    </a></li>
                            </ul>
                            <div class="balance-menu-footer">
                                <a href="{{ route('user.balance-topup') }}" class="btn btn-primary balance-topup-btn">
                                    <i class="fas fa-plus-circle"></i>
                                    {{ __('Пополнить баланс') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button class="control-btn theme-toggle" data-toggle="theme" title="{{ __('Переключить тему') }}">
                        <span class="theme-icon theme-icon-light"><i class="fas fa-moon"></i></span>
                        <span class="theme-icon theme-icon-dark"><i class="fas fa-sun"></i></span>
                    </button>

                    <!-- Language Selector -->
                    <div class="control-dropdown language-selector">
                        <button class="control-btn" data-toggle="dropdown">
                            <img src="{{ asset('assets/flags/' . app()->getLocale() . '.svg') }}"
                                alt="{{ app()->getLocale() }}" class="flag-icon">
                            <span>{{ strtoupper(app()->getLocale()) === 'RU' ? 'РУ' : strtoupper(app()->getLocale()) }}</span>
                            <i class="fas fa-chevron-down language-arrow"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-lang="ru"><img src="{{ asset('assets/flags/ru.svg') }}"
                                        alt="RU" class="flag-icon">
                                    <span>РУ</span>
                                </a>
                            </li>
                            <li><a href="#" data-lang="en"><img src="{{ asset('assets/flags/en.svg') }}"
                                        alt="EN" class="flag-icon">
                                    <span>EN</span>
                                </a>
                            </li>
                            <li><a href="#" data-lang="cn"><img src="{{ asset('assets/flags/cn.svg') }}"
                                        alt="CN" class="flag-icon">
                                    <span>中文</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Notifications -->
                    <div class="control-dropdown notifications-selector">
                        <button class="control-btn notifications-btn" data-toggle="dropdown"
                            title="{{ __('Уведомления') }}">
                            <div class="notification-icon-wrapper">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge" data-count="3" id="notificationsBadge">3</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu notifications-menu">
                            <li class="notifications-header">
                                <span class="notifications-title">{{ __('Уведомления') }}</span>
                                <button class="mark-all-read-btn" onclick="markAllNotificationsRead()"
                                    data-bs-toggle="tooltip" data-bs-title="{{ __('Отметить все как прочитанные') }}">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            </li>
                            <li class="notifications-divider"></li>
                            <li class="notifications-list" id="notificationsList">
                                <!-- Notifications will be loaded here by JavaScript -->
                            </li>
                            <li class="notifications-divider"></li>
                            <li class="notifications-footer">
                                <a href="{{ route('user.notifications') }}"
                                    class="view-all-notifications">{{ __('Посмотреть все уведомления') }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="control-dropdown profile-selector">
                        <button class="control-btn profile-btn" data-toggle="dropdown">
                            <div class="profile-avatar">
                                {{-- <i class="fas fa-user"></i> --}}
                                <img src="{{ asset('assets/images/avatar.jpg') }}" alt="Avatar">
                            </div>
                            <span class="profile-name">Александр</span>
                            <i class="fas fa-chevron-down profile-arrow"></i>
                        </button>
                        <ul class="dropdown-menu profile-menu">
                            <li>
                                <a href="{{ route('user.balance-topup') }}" class="profile-option">
                                    <i class="fas fa-wallet"></i>
                                    <span>{{ __('Пополнить баланс') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.orders') }}" class="profile-option">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>{{ __('Заказы') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.tickets') }}" class="profile-option">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>{{ __('Тикеты') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.profile') }}" class="profile-option">
                                    <i class="fas fa-cog"></i>
                                    <span>{{ __('Настройки') }}</span>
                                </a>
                            </li>
                            <li class="profile-divider"></li>
                            <li>
                                <button class="profile-option logout-option" data-bs-toggle="modal"
                                    data-bs-target="#logoutConfirmModal">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ __('Выйти из аккаунта') }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button class="navbar-toggle" type="button" data-toggle="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Navigation Menu (hidden on mobile by default) -->
                <div class="navbar-menu" id="navbar-menu">

                    <!-- Mobile additional controls -->
                    <div class="mobile-additional-controls">

                        <!-- Balance Selector -->
                        <div class="balance-selector">
                            <button class="balance-btn" data-toggle="dropdown">
                                <span class="balance-icon" data-currency="usd">$</span>
                                <span class="balance-amount" data-currency="usd">1,250.00</span>
                                <i class="fas fa-chevron-down balance-arrow"></i>
                            </button>
                            <div class="dropdown-menu balance-dropdown-menu">
                                <ul class="balance-list">
                                    <li><a href="#" class="balance-option active" data-currency="usd">
                                            <span class="currency-icon">$</span>
                                            <span class="currency-name">USD</span>
                                            <span class="currency-amount">1,250.00</span>
                                        </a></li>
                                    <li><a href="#" class="balance-option" data-currency="eur">
                                            <span class="currency-icon">€</span>
                                            <span class="currency-name">EUR</span>
                                            <span class="currency-amount">1,150.75</span>
                                        </a></li>
                                    <li><a href="#" class="balance-option" data-currency="rub">
                                            <span class="currency-icon">₽</span>
                                            <span class="currency-name">RUB</span>
                                            <span class="currency-amount">95,200.50</span>
                                        </a></li>
                                    <li><a href="#" class="balance-option" data-currency="cny">
                                            <span class="currency-icon">¥</span>
                                            <span class="currency-name">CNY</span>
                                            <span class="currency-amount">8,950.25</span>
                                        </a></li>
                                </ul>
                                <div class="balance-menu-footer">
                                    <a href="{{ route('user.balance-topup') }}"
                                        class="btn btn-primary balance-topup-btn">
                                        <i class="fas fa-plus-circle"></i>
                                        {{ __('Пополнить баланс') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="control-dropdown profile-selector">
                            <button class="control-btn profile-btn" data-toggle="dropdown">
                                <div class="profile-avatar">
                                    {{-- <i class="fas fa-user"></i> --}}
                                    <img src="{{ asset('assets/images/avatar.jpg') }}" alt="Avatar">
                                </div>
                                <span class="profile-name">Александр</span>
                                <i class="fas fa-chevron-down profile-arrow"></i>
                            </button>
                            <ul class="dropdown-menu profile-menu">
                                <li>
                                    <a href="{{ route('user.balance-topup') }}" class="profile-option">
                                        <i class="fas fa-wallet"></i>
                                        <span>{{ __('Пополнить баланс') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.orders') }}" class="profile-option">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>{{ __('Заказы') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.tickets') }}" class="profile-option">
                                        <i class="fas fa-ticket-alt"></i>
                                        <span>{{ __('Тикеты') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.profile') }}" class="profile-option">
                                        <i class="fas fa-cog"></i>
                                        <span>{{ __('Настройки') }}</span>
                                    </a>
                                </li>
                                <li class="profile-divider"></li>
                                <li>
                                    <button class="profile-option logout-option" data-bs-toggle="modal"
                                        data-bs-target="#logoutConfirmModal">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>{{ __('Выйти из аккаунта') }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <nav class="sidebar-nav">
                        <ul class="sidebar-nav_list">
                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.orders') ? 'active' : '' }}"
                                    href="{{ route('user.orders') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Создать заказ') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.orders-history') ? 'active' : '' }}"
                                    href="{{ route('user.orders-history') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('История заказов') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.services') ? 'active' : '' }}"
                                    href="{{ route('user.services') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Услуги') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.balance-topup') ? 'active' : '' }}"
                                    href="{{ route('user.balance-topup') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Пополнение баланса') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.tickets') ? 'active' : '' }}"
                                    href="{{ route('user.tickets') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Поддержка') }}</span>
                                    <span class="sidebar-nav_badge">2</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.updates') ? 'active' : '' }}"
                                    href="{{ route('user.updates') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Обновления') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.referral') ? 'active' : '' }}"
                                    href="{{ route('user.referral') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Реферальная программа') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link" href="{{ route('news.index') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Новости') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.api') ? 'active' : '' }}"
                                    href="{{ route('user.api') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-code"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('API') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item">
                                <a class="sidebar-nav_link {{ request()->routeIs('user.profile') ? 'active' : '' }}"
                                    href="{{ route('user.profile') }}">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Профиль') }}</span>
                                </a>
                            </li>

                            <li class="sidebar-nav_item sidebar-nav_item--logout">
                                <button class="sidebar-nav_link sidebar-nav_link--logout" type="button"
                                    data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                                    <div class="sidebar-nav_icon">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <span class="sidebar-nav_text">{{ __('Выйти из аккаунта') }}</span>
                                </button>
                            </li>
                        </ul>
                    </nav>

                </div>
            </nav>
        </div>
    </header>
@endauth
