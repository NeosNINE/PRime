@extends('admin.app.layout')
@section('title', $title ?? 'Dev Tools' )

@section('content')

    <div class="container">

        <div class="page-header">
            <h1>{{ $title ?? 'Dev Tools' }}</h1>
        </div>
        <div class="box no-padding">

            <div class="box-type-choose">
                @foreach( devTools()->getNavigation() as $link )

                    <a
                            href="{{ $link['href'] }}"
                            @if( str($link['href'])->endsWith(  request()->path() ) ) class="active" @endif
                            @if( isset($link['target']) ) target="{{ $link['target'] }}" @endif
                    >{!! $link['name'] !!}</a>

                @endforeach
            </div>

            @yield('dev_tools_content')

        </div>

        @yield('dev_tools_content_second')

    </div>

    <load-js src="{{ asset('assets/admin/js/pages/dev_tools.js') }}"></load-js>

@endsection
