@extends('admin.app.layout')

@section('content')
    <div class="container">

        <div class="page-error-block">
            <h1>@yield('code', __('Oh no'))</h1>
            <p>@yield('message')</p>
        </div>

    </div>
@endsection
