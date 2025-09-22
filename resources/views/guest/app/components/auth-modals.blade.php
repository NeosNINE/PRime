<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content auth-modal">
            <div class="modal-header auth-modal-header">
                <h5 class="modal-title auth-modal-title" id="loginModalLabel">{{ __('Авторизация') }}</h5>
            </div>
            <div class="modal-body auth-modal-body">
                <form id="loginForm" class="auth-form" novalidate>
                    @csrf

                    <!-- Email Field -->
                    <div class="auth-form-group">
                        <label for="loginEmail" class="auth-form-label">{{ __('Логин или email') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-envelope auth-form-icon"></i>
                            <input type="text" class="auth-form-input" id="loginEmail" name="email"
                                placeholder="{{ __('Введите логин или email') }}" required>
                        </div>
                        <div class="auth-form-error" id="loginEmailError"></div>
                    </div>

                    <!-- Password Field -->
                    <div class="auth-form-group">
                        <label for="loginPassword" class="auth-form-label">{{ __('Пароль') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-lock auth-form-icon"></i>
                            <input type="password" class="auth-form-input" id="loginPassword" name="password"
                                placeholder="{{ __('Введите ваш пароль') }}" required>
                            <button type="button" class="auth-form-toggle-password" data-target="loginPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-form-error" id="loginPasswordError"></div>
                    </div>

                    <!-- Remember Me -->
                    <div class="auth-form-checkbox-wrapper">
                        <div class="auth-form-checkbox">
                            <input type="checkbox" id="rememberMe" name="remember" class="auth-checkbox">
                            <label for="rememberMe" class="auth-checkbox-label">{{ __('Запомнить меня') }}</label>
                        </div>
                        <button class="auth-forgot-link" data-auth-open="forgot">
                            {{ __('Забыли пароль?') }}
                        </button>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="auth-form-group">
                        <div class="g-recaptcha" data-sitekey="6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"></div>
                        <div class="auth-form-error" id="loginRecaptchaError"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary auth-form-submit" id="loginSubmit">
                        <span class="auth-form-submit-text">{{ __('Войти') }}</span>
                        <span class="auth-form-submit-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('Вход...') }}
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>{{ __('или') }}</span>
                    </div>

                    <!-- Google OAuth -->
                    <button type="button" class="btn auth-google-btn" id="loginWithGoogle">
                        <svg class="auth-google-icon" viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"
                                fill="#4285F4" />
                            <path
                                d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"
                                fill="#34A853" />
                            <path
                                d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"
                                fill="#FBBC05" />
                            <path
                                d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"
                                fill="#EB4335" />
                        </svg>
                        {{ __('Войти через Google') }}
                    </button>

                    <!-- Register Link -->
                    <div class="auth-switch">
                        <span>{{ __('Нет аккаунта?') }}</span>
                        <button data-auth-open="register">
                            {{ __('Зарегистрироваться') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content auth-modal">
            <div class="modal-header auth-modal-header">
                <h5 class="modal-title auth-modal-title" id="registerModalLabel">{{ __('Регистрация') }}</h5>
            </div>
            <div class="modal-body auth-modal-body">
                <form id="registerForm" class="auth-form" novalidate>
                    @csrf

                    <!-- Login Field -->
                    <div class="auth-form-group">
                        <label for="registerLogin" class="auth-form-label">{{ __('Логин') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-user auth-form-icon"></i>
                            <input type="text" class="auth-form-input" id="registerLogin" name="login"
                                placeholder="{{ __('Введите логин') }}" required>
                        </div>
                        <div class="auth-form-error" id="registerLoginError"></div>
                    </div>

                    <!-- Email Field -->
                    <div class="auth-form-group">
                        <label for="registerEmail" class="auth-form-label">{{ __('Email') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-envelope auth-form-icon"></i>
                            <input type="email" class="auth-form-input" id="registerEmail" name="email"
                                placeholder="{{ __('Введите email') }}" required>
                        </div>
                        <div class="auth-form-error" id="registerEmailError"></div>
                    </div>

                    <!-- Password Field -->
                    <div class="auth-form-group">
                        <label for="registerPassword" class="auth-form-label">{{ __('Пароль') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-lock auth-form-icon"></i>
                            <input type="password" class="auth-form-input" id="registerPassword" name="password"
                                placeholder="{{ __('Минимум 8 символов') }}" required>
                            <button type="button" class="auth-form-toggle-password" data-target="registerPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-form-error" id="registerPasswordError"></div>
                        <div class="auth-password-strength" id="passwordStrength">
                            <div class="strength-bar">
                                <div class="strength-fill"></div>
                            </div>
                            <div class="strength-text"></div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="auth-form-group">
                        <label for="registerPasswordConfirm"
                            class="auth-form-label">{{ __('Повторите пароль') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-lock auth-form-icon"></i>
                            <input type="password" class="auth-form-input" id="registerPasswordConfirm"
                                name="password_confirmation" placeholder="{{ __('Повторите пароль') }}" required>
                            <button type="button" class="auth-form-toggle-password"
                                data-target="registerPasswordConfirm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="auth-form-error" id="registerPasswordConfirmError"></div>
                    </div>

                    <!-- Terms and Privacy -->
                    <div class="auth-form-checkbox-wrapper">
                        <div class="auth-form-checkbox">
                            <input type="checkbox" id="acceptTerms" name="terms" class="auth-checkbox" required>
                            <label for="acceptTerms" class="auth-checkbox-label">
                                {{ __('Я принимаю') }}
                                <a href="{{ route('rules') }}">{{ __('Правила сервиса') }}</a>
                                {{ __('и') }}
                                <a href="{{ route('policy') }}">{{ __('Политику конфиденциальности') }}</a>
                            </label>
                        </div>
                    </div>
                    <div class="auth-form-error" id="registerTermsError"></div>

                    <!-- reCAPTCHA -->
                    <div class="auth-form-group">
                        <div class="g-recaptcha" data-sitekey="6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"></div>
                        <div class="auth-form-error" id="registerRecaptchaError"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary auth-form-submit" id="registerSubmit">
                        <span class="auth-form-submit-text">{{ __('Создать аккаунт') }}</span>
                        <span class="auth-form-submit-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('Создание...') }}
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>{{ __('или') }}</span>
                    </div>

                    <!-- Google OAuth -->
                    <button type="button" class="btn auth-google-btn" id="registerWithGoogle">
                        <svg class="auth-google-icon" viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"
                                fill="#4285F4" />
                            <path
                                d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"
                                fill="#34A853" />
                            <path
                                d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"
                                fill="#FBBC05" />
                            <path
                                d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"
                                fill="#EB4335" />
                        </svg>
                        {{ __('Зарегистрироваться через Google') }}
                    </button>

                    <!-- Login Link -->
                    <div class="auth-switch">
                        <span>{{ __('Уже есть аккаунт?') }}</span>
                        <button data-auth-open="login">
                            {{ __('Войти') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 2FA Modal -->
<div class="modal fade" id="twoFactorModal" tabindex="-1" aria-labelledby="twoFactorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content auth-modal">
            <div class="modal-header auth-modal-header">
                <h5 class="modal-title auth-modal-title" id="twoFactorModalLabel">
                    {{ __('Двухфакторная аутентификация') }}</h5>
            </div>
            <div class="modal-body auth-modal-body">
                <div class="auth-2fa-info">
                    <div class="auth-2fa-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p>{{ __('Введите 6-значный код из приложения Google Authenticator') }}</p>
                </div>

                <form id="twoFactorForm" class="auth-form" novalidate>
                    @csrf
                    <input type="hidden" name="temp_token" id="tempToken" value="">

                    <!-- 2FA Code Field -->
                    <div class="auth-form-group">
                        <label for="twoFactorCode" class="auth-form-label">{{ __('Код аутентификации') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-key auth-form-icon"></i>
                            <input type="text" class="auth-form-input auth-2fa-input" id="twoFactorCode"
                                name="code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                        </div>
                        <div class="auth-form-error" id="twoFactorCodeError"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary auth-form-submit" id="twoFactorSubmit">
                        <span class="auth-form-submit-text">{{ __('Подтвердить') }}</span>
                        <span class="auth-form-submit-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('Проверка...') }}
                        </span>
                    </button>

                    <!-- Back Link -->
                    <div class="auth-switch">
                        <button data-auth-open="login">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Вернуться к входу') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content auth-modal">
            <div class="modal-header auth-modal-header">
                <h5 class="modal-title auth-modal-title" id="forgotPasswordModalLabel">{{ __('Сброс пароля') }}</h5>
            </div>
            <div class="modal-body auth-modal-body">
                <div class="auth-forgot-info">
                    <p>{{ __('Введите ваш email и мы отправим ссылку для сброса пароля') }}</p>
                </div>

                <form id="forgotPasswordForm" class="auth-form" novalidate>
                    @csrf

                    <!-- Email Field -->
                    <div class="auth-form-group">
                        <label for="forgotEmail" class="auth-form-label">{{ __('Email') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-envelope auth-form-icon"></i>
                            <input type="email" class="auth-form-input" id="forgotEmail" name="email"
                                placeholder="{{ __('Введите ваш email') }}" required>
                        </div>
                        <div class="auth-form-error" id="forgotEmailError"></div>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="auth-form-group">
                        <div class="g-recaptcha" data-sitekey="6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"></div>
                        <div class="auth-form-error" id="forgotRecaptchaError"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary auth-form-submit" id="forgotPasswordSubmit">
                        <span class="auth-form-submit-text">{{ __('Отправить ссылку') }}</span>
                        <span class="auth-form-submit-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('Отправка...') }}
                        </span>
                    </button>

                    <!-- Back Link -->
                    <div class="auth-switch">
                        <button data-auth-open="login">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Вернуться к входу') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
