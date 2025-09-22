@section('title', env('APP_NAME').' | '. __('Сброс пароля') )
@extends('guest.app.layout')

@section('content')

    <div class="auth-section">
        <div class="container">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h1 class="auth-card-title">{{ __('Сброс пароля') }}</h1>
                    <p class="auth-card-subtitle">{{ __('Введите новый пароль для вашего аккаунта') }}</p>
                </div>

                @include('auth.errors')

                <form method="POST" action="{{ route('password.update') }}" class="auth-form" id="resetPasswordForm" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <input type="hidden" name="email" id="resetEmail" value="{{ old('email', $request->email) }}">
                    @error('email')
                        <div class="auth-form-error show" id="resetEmailError">{{ $message }}</div>
                    @else
                        <div class="auth-form-error" id="resetEmailError"></div>
                    @enderror

                    <div class="auth-form-group">
                        <label for="password" class="auth-form-label">{{ __('Новый пароль') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-lock auth-form-icon"></i>
                            <input type="password" class="auth-form-input" id="password" name="password" placeholder="{{ __('Минимум 8 символов') }}" required autofocus>
                        </div>
                        @error('password')
                            <div class="auth-form-error show" id="resetPasswordError">{{ $message }}</div>
                        @else
                            <div class="auth-form-error" id="resetPasswordError"></div>
                        @enderror
                    </div>

                    <div class="auth-form-group">
                        <label for="password_confirmation" class="auth-form-label">{{ __('Повторите пароль') }}</label>
                        <div class="auth-form-input-wrapper">
                            <i class="fas fa-lock auth-form-icon"></i>
                            <input type="password" class="auth-form-input" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Повторите пароль') }}" required>
                        </div>
                        @error('password_confirmation')
                            <div class="auth-form-error show" id="resetPasswordConfirmError">{{ $message }}</div>
                        @else
                            <div class="auth-form-error" id="resetPasswordConfirmError"></div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary auth-form-submit" id="resetSubmit">
                        <span class="auth-form-submit-text">{{ __('Сбросить пароль') }}</span>
                        <span class="auth-form-submit-loading"><i class="fas fa-spinner fa-spin"></i>{{ __('Сохранение...') }}</span>
                    </button>
                </form>

                <div class="auth-switch">
                    <a href="{{ route('login') }}">{{ __('Вернуться к входу') }}</a>
                </div>
            </div>
        </div>
    </div>

@endsection
