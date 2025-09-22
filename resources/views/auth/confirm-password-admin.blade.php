@section('title', env('APP_NAME').' | Confirm Password' )
@extends('admin.app.layout')

@section('content')

    <div class="container">
        <div class="page-header">
            <h1>Авторизация</h1>
        </div>
        <div class="box">
            <div class="box-header">
                Это защищенная область приложения. Пожалуйста, подтвердите свой пароль, прежде чем продолжить.
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <input type="hidden" name="backTo" value="{{ request()->input('backTo') }}">

                    <div class="input">
                        <label>
                            <span class="label">Пароль</span>
                            <input type="password" name="password" required autofocus>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-default">Подтвердить</button>

                </form>

                <div class="color-error mt-10">
                    @include('auth.status')
                    @include('auth.errors')
                </div>

            </div>
        </div>
    </div>

@endsection
