@extends('admin.app.layout')
@section('title', 'Добавить услугу' )

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Добавить услугу</h1>
        </div>

        <div class="box">
            <form method="POST" action="{{ route('admin.services.add.save') }}">
                @csrf
                @include('admin.app.services.components.form', [
                    'service' => null,
                    'providers' => $providers,
                    'categories' => $categories,
                    'mode' => 'create',
                ])
            </form>
        </div>
    </div>
@endsection
