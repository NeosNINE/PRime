@extends('admin.app.dev_tools.layout')

@section('dev_tools_content')

    <div class="box-body">
        <span data-prism disabled>{{ $content }}</span>
    </div>

@endsection
