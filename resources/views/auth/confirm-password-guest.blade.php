@section('title', env('APP_NAME').' | Confirm Password' )
@extends('guest.app.layout')

@section('content')

    <div class="box">

        <h1>Это защищенная область приложения. Пожалуйста, подтвердите свой пароль, прежде чем продолжить.</h1>

        @include('auth.status')
        @include('auth.errors')

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <input type="hidden" name="backTo" value="{{ request()->input('backTo') }}">

            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>

            <button type="submit" class="btn btn-default">Confirm</button>

        </form>

    </div>

@endsection
