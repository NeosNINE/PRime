@extends('admin.app.layout')
@section('title', $title ?? 'Настройки' )

@section('content')


    <div class="container page-settings">

        <div class="page-header">
            <h1>{{ $title ?? 'Настройки' }}</h1>
        </div>
        <div class="box no-padding">

            @if( count( settings()->getNavLinks() ) > 1 )
                <div class="box-type-choose">
                    @foreach( settings()->getNavLinks() as $link )

                        <a href="{{ $link['href'] }}" @if( str($link['href'])->endsWith(  request()->path() ) ) class="active" @endif >{{ $link['name'] }}</a>

                    @endforeach
                </div>
            @endif


            @yield('settings_content')

        </div>

    </div>

@endsection
