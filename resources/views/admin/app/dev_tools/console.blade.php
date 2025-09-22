@extends('admin.app.dev_tools.layout')

@section('dev_tools_content')

    <form class="console-command-form">
        <div class="box-body">
            <p>С помощью данного инструмента можно выполнять Artisan команды.</p>
            <div class="input">
                <label>
                    <span class="label">Command</span>
                    <input type="text" name="console-command" autofocus autocomplete="password">
                </label>
            </div>
            <button class="btn btn-primary mb-3">Run</button>
            <div class="list-console-commands">
                @foreach( $commands as $command )
                    <div>
                        <a href="#" class="btn" data-console-btn="{{ $command->getName() }}"><b>{{ $command->getName() }}</b><span class="text-muted">{{ $command->getDescription() }}</span></a>
                    </div>
                @endforeach
            </div>
        </div>
    </form>

@endsection
