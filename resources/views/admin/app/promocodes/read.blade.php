@extends('admin.app.layout')
@section('title', 'Промокод')

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form class="inputs-only-read">
            <div class="page-header">
                <h1>Промокод #{{ $item->id }}</h1>
            </div>

            <div class="box">
                <div class="box-body">
                    @include('admin.app.promocodes.components.form', ['item' => $item, 'is_read' => true])
                </div>
            </div>
        </form>

        <div class="page-footer">
            <div class="actions">
                @if( roles()->checkAccess('promo_codes.edit') )
                    <a data-offcanvas-href="{{ route('admin.promocodes.edit', $item) }}" class="btn btn-primary"><i class="fa fa-pen-to-square"></i> <span>Редактировать</span></a>
                @endif
            </div>
        </div>

    </div>

@endsection


