@extends('admin.app.layout')
@section('title', 'Редактировать услугу' )

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Редактировать услугу</h1>
        </div>

        <div class="box">
            <form method="POST" action="{{ route('admin.services.edit.save', $service) }}">
                @csrf
                @method('PUT')
                @include('admin.app.services.components.form', [
                    'service' => $service,
                    'providers' => $providers,
                    'categories' => $categories,
                    'mode' => 'edit',
                ])
            </form>
        </div>
    </div>
@endsection
