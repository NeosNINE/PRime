@section('title', env('APP_NAME').' | Reset Password' )
@extends('guest.app.layout')

@section('content')

    <div class="box">

        <h1>Reset Password</h1>

        @include('auth.status')
        @include('auth.errors')

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>

            <button type="submit" class="btn btn-default">Reset Password</button>

        </form>

    </div>
    <div class="extra-info">
        <a href="{{ route('login') }}"> &larr; Back to Log in</a>
    </div>


@endsection
