<div class="sidebar">
    <div class="sidebar_inner">
        <div class="sidebar_top">
            <button class="sidebar_open">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-nav_list">
                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.orders') ? 'active' : '' }}" href="{{ route('user.orders') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Создать заказ') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.orders-history') ? 'active' : '' }}" href="{{ route('user.orders-history') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('История заказов') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.services') ? 'active' : '' }}" href="{{ route('user.services') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Услуги') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.balance-topup') ? 'active' : '' }}" href="{{ route('user.balance-topup') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Пополнение баланса') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.tickets') ? 'active' : '' }}" href="{{ route('user.tickets') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Поддержка') }}</span>
                        <span class="sidebar-nav_badge">2</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.updates') ? 'active' : '' }}" href="{{ route('user.updates') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Обновления') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.referral') ? 'active' : '' }}" href="{{ route('user.referral') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Реферальная программа') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link" href="{{ route('news.index')}}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Новости') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.api') ? 'active' : '' }}" href="{{ route('user.api') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('API') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item">
                    <a class="sidebar-nav_link {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Профиль') }}</span>
                    </a>
                </li>

                <li class="sidebar-nav_item sidebar-nav_item--logout">
                    <button class="sidebar-nav_link sidebar-nav_link--logout" type="button" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                        <div class="sidebar-nav_icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span class="sidebar-nav_text">{{ __('Выйти из аккаунта') }}</span>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</div>