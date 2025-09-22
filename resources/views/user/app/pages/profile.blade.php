@section('title', 'Профиль - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="profile-page">
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- User Info Section -->
            <div class="user-info-section">
                <!-- Avatar -->
                <div class="user-avatar-container">
                    <div class="user-avatar" id="userAvatar" onclick="document.getElementById('avatarInput').click()">
                        <img id="avatarImage" src="{{ $user->avatar }}" alt="Avatar" style="{{ $user->has_avatar ? '' : 'display: none;' }}">
                        <div class="avatar-placeholder" id="avatarPlaceholder" style="{{ $user->has_avatar ? 'display: none;' : '' }}">
                            <span class="avatar-initials">{{ $user->avatar_initials }}</span>
                        </div>
                        <div class="avatar-upload-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                        <input type="file" id="avatarInput" accept="image/png,image/jpg,image/jpeg"
                            style="display: none;">
                    </div>
                    <button type="button" class="avatar-upload-btn"
                        onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-upload"></i>
                        {{ __('Загрузить аватар') }}
                    </button>
                </div>

                <!-- User Basic Info -->
                <div class="user-basic-info">
                    <h1 class="user-name">{{ $user->login ?? 'login' }}</h1>
                    <p class="user-email">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Profile Tabs -->
            <div class="profile-tabs">
                <button class="tab-btn active" data-tab="email">
                    <i class="fas fa-envelope"></i>
                    {{ __('Email') }}
                </button>
                <button class="tab-btn" data-tab="password">
                    <i class="fas fa-lock"></i>
                    {{ __('Пароль') }}
                </button>
                <button class="tab-btn" data-tab="2fa">
                    <i class="fas fa-shield-alt"></i>
                    {{ __('2FA') }}
                </button>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Email Tab -->
            <div class="tab-content active" id="email-tab">
                <div class="content-card">
                    <h2 class="section-title">{{ __('Изменить Email') }}</h2>

                    <form id="emailForm" class="profile-form">
                        @csrf
                        <div class="form-group">
                            <label for="currentEmail" class="form-label">{{ __('Текущий email') }}</label>
                            <input type="email" id="currentEmail" class="form-input"
                                value="{{ $user->email }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="newEmail" class="form-label">{{ __('Новый email') }}</label>
                            <input type="email" id="newEmail" class="form-input"
                                placeholder="{{ __('Введите новый email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="currentPasswordEmail" class="form-label">{{ __('Текущий пароль') }}</label>
                            <div class="password-input-container">
                                <input type="password" id="currentPasswordEmail" class="form-input password-input"
                                    placeholder="••••••••••••••" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('currentPasswordEmail')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Изменить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Tab -->
            <div class="tab-content" id="password-tab">
                <div class="content-card">
                    <h2 class="section-title">{{ __('Изменить пароль') }}</h2>

                    <form id="passwordForm" class="profile-form">
                        @csrf
                        <div class="form-group">
                            <label for="currentPassword" class="form-label">{{ __('Текущий пароль') }}</label>
                            <div class="password-input-container">
                                <input type="password" id="currentPassword" class="form-input password-input"
                                    placeholder="••••••••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="newPassword" class="form-label">{{ __('Новый пароль') }}</label>
                            <div class="password-input-container">
                                <input type="password" id="newPassword" class="form-input password-input"
                                    placeholder="••••••••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">{{ __('Повторите пароль') }}</label>
                            <div class="password-input-container">
                                <input type="password" id="confirmPassword" class="form-input password-input"
                                    placeholder="••••••••••••••" required>
                                <button type="button" class="password-toggle"
                                    onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Изменить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2FA Tab -->
            <div class="tab-content" id="2fa-tab">
                <div class="content-card">
                    <h2 class="section-title">{{ __('Двухфакторная аутентификация') }}</h2>

                    <div class="tfa-status-section">
                        <div class="tfa-status">
                            <div class="status-indicator">
                                <div class="status-icon" id="tfaStatusIcon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="status-content">
                                    <div class="status-title" id="tfaStatusTitle">{{ __('2FA отключена') }}</div>
                                    <div class="status-description" id="tfaStatusDescription">
                                        {{ __('Для дополнительной безопасности рекомендуем включить двухфакторную аутентификацию') }}
                                    </div>
                                </div>
                            </div>
                            <div class="tfa-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="tfaToggle" onchange="toggleTFA()">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 2FA Setup Section (hidden by default) -->
                    <div class="tfa-setup-section" id="tfaSetupSection" style="display: none;">
                        <div class="setup-step">
                            <h3 class="step-title">{{ __('Шаг 1: Скачайте Google Authenticator') }}</h3>
                            <p class="step-description">
                                {{ __('Установите приложение Google Authenticator на ваш телефон или воспользуйтесь расширением в браузере Chrome') }}
                            </p>
                            <div class="app-links">
                                <a class="app-link" href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">
                                    <i class="fab fa-apple"></i>
                                    App Store
                                </a>
                                <a class="app-link" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
                                    <i class="fab fa-google-play"></i>
                                    Google Play
                                </a>
                                <a class="app-link" href="https://chromewebstore.google.com/detail/authenticator/bhghoamapcdpbohphigoooaddinpkbai" target="_blank">
                                    <i class="fa-brands fa-chrome"></i>
                                    Chrome
                                </a>
                            </div>
                        </div>

                        <div class="setup-step">
                            <h3 class="step-title">{{ __('Шаг 2: Отсканируйте QR-код') }}</h3>
                            <p class="step-description">
                                {{ __('Отсканируйте QR-код с помощью приложения Google Authenticator') }}</p>
                            <div class="qr-code-container">
                                <div class="qr-code">
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2ZmZiIvPgogIDx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5RUiBDb2RlPC90ZXh0Pgo8L3N2Zz4K"
                                        alt="QR Code">
                                </div>
                                <div class="manual-key">
                                    <p class="manual-key-label">{{ __('Или введите ключ вручную:') }}</p>
                                    <div class="manual-key-value">
                                        <div id="manualKey" class="manual-key-text">XXXXXXXXXXXXXXXXXXXX</div>
                                        <button type="button" class="copy-btn" onclick="copyManualKey()"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="{{ __('Скопировать') }}">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="setup-step">
                            <h3 class="step-title">{{ __('Шаг 3: Введите код подтверждения') }}</h3>
                            <p class="step-description">
                                {{ __('Введите 6-значный код из приложения Google Authenticator') }}</p>
                            <form id="tfaSetupForm" class="tfa-form">
                                @csrf
                                <div class="form-group">
                                    <label for="tfaCode" class="form-label">{{ __('Код подтверждения') }}</label>
                                    <input type="text" id="tfaCode" class="form-input tfa-code-input"
                                        placeholder="123456" maxlength="6" pattern="[0-9]{6}" required>
                                </div>
                                <div class="form-submit">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Включить 2FA') }}
                                    </button>
                                    <button type="button" class="btn btn-outline" onclick="cancelTfaProcess()">
                                        {{ __('Отмена') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2FA Disable Section (hidden by default) -->
                    <div class="tfa-disable-section" id="tfaDisableSection" style="display: none;">
                        <form id="tfaDisableForm" class="tfa-form">
                            @csrf
                            <div class="form-group">
                                <label for="tfaDisableCode"
                                    class="form-label">{{ __('Введите код из Google Authenticator для отключения 2FA') }}</label>
                                <input type="text" id="tfaDisableCode" class="form-input tfa-code-input"
                                    placeholder="123456" maxlength="6" pattern="[0-9]{6}" required>
                            </div>
                            <div class="form-submit">
                                <button type="submit" class="btn btn-danger">
                                    {{ __('Отключить 2FA') }}
                                </button>
                                <button type="button" class="btn btn-outline" onclick="cancelTfaProcess()">
                                    {{ __('Отмена') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/profile.js') }}"></script>
@endpush
