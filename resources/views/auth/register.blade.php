@section('title', env('APP_NAME').' | Register' )
@extends('guest.app.layout')

@section('content')

    <div class="box">

        <h1>Register</h1>

        @include('auth.errors')

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>

            <label>
                <span>Confirm Password</span>
                <input type="password" name="password_confirmation" required>
            </label>

            <button type="submit" class="btn btn-default">Register</button>

            <hr>

            <a href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

        </form>

    </div>
    <div class="extra-info">
        <a href="{{ route('index') }}"> &larr; Back to Home Page</a>
    </div>


@endsection
