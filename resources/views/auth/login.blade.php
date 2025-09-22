@section('title', env('APP_NAME').' | Log in' )
@extends('guest.app.layout')

@section('content')

    <div class="box">

        <h1>Log in</h1>

        @include('auth.status')
        @include('auth.errors')

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="hidden" name="backTo" value="{{ request()->input('backTo') }}">

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>

            <label>
                <input type="checkbox" name="remember"> remember me
            </label>

            <button type="submit" class="btn btn-default">Log in</button>

            <hr>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
            <p><a href="{{ route('register') }}">Create account</a></p>

        </form>

    </div>
    <div class="extra-info">
        <a href="{{ route('index') }}"> &larr; Back to Home Page</a>
    </div>


@endsection
