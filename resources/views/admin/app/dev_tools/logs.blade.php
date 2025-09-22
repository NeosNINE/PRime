@extends('admin.app.dev_tools.layout')

@section('dev_tools_content')

    <div class="box-type-choose">
        <a href="{{ route('admin.dev_tools.logs') }}" class="text-default @if( !request()->input('type') ) active @endif">All</a>
        @foreach( devTools()->getLogTypes() as $log_type )
            <a href="{{ route('admin.dev_tools.logs', ['type' => $log_type]) }}" class="text-{{ devTools()->getColorClassLogType($log_type) }}  @if( request()->input('type') == $log_type ) active @endif">{{ $log_type }}</a>
        @endforeach

        @if( count($logs) )
            <button class="btn btn-warning ml-auto btn-action" data-action="{{ route('admin.dev_tools.clear_logs') }}" @if( isProduction() ) data-confirm @endif data-success-refresh-page>Отчистить логи</button>
        @endif
    </div>

@endsection

@section('dev_tools_content_second')

    <div class="devtools-logs-page">

            @forelse( $logs as $log )
                <div class="box no-padding">
                    <div class="box-header">
                        <div class="left-content">
                            <div class="d-label label-{{ $log['color'] }} mr-10">{{ $log['type'] }}</div>
                            <div class="mt-5 text-muted">{{ $log['date']->calendar() }}</div>
                        </div>
                        <div class="right-content">
                            @if( isset($log['errorId']) )
                                <div class="text-muted mr-10 errorId" title="Error ID" data-copy-text>{{ $log['errorId'] }}</div>
                            @endif
                            <div>
                                <a href="phpstorm://open?file={{ $log['log_file_path'] }}" target="_blank"><i class="fa-regular fa-file" title="{{ $log['log_file'] }}"></i></a>
                            </div>
                            @if( $log['count'] > 1 )
                                <div class="text-muted ml-10" title="Кол-во записей в логах">{{ $log['count'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <pre class="log-msg {{ $log['type'] }}">{{ $log['msg'] }}</pre>
                        @if( $log['sql'] )
                            <div class="code" data-prism data-not-line-numbers data-lang="sql">{{ $log['sql'] }}</div>
                        @endif
                        @if( $log['first_trace'] || $log['first_trace_code'] )
                            <div class="first-log-stacktrace">
                                @if( $log['first_trace'] )
                                    <div class="file_name">
                                        <i class="far fa-file-alt mr-3"></i>
                                        <a href="phpstorm://open?file={{ urlencode($log['first_trace']['file_full_path']) }}&line={{ $log['first_trace']['line'] }}" target="_blank">{{ $log['first_trace']['file'] }}:{{ $log['first_trace']['line'] }}</a>
                                        @if( $log['first_trace']['msg'] )
                                            <p>{{ $log['first_trace']['msg'] }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if( $log['first_trace_code'] )
                                    <div class="code" data-prism data-start="{{ $log['first_trace_code']['first_line'] }}" data-line="{{ $log['first_trace_code']['line'] }}">{{ $log['first_trace_code']['code'] }}</div>
                                @endif
                            </div>
                        @endif
                        @if( $log['stacktrace'] || count($log['context'] ?? []) )
                        <div class="row">
                            <div class="log-stacktrace col-xl-40">
                                <h6>Stacktrace</h6>
                                @if( $log['stacktrace'] )
                                    @foreach( $log['stacktrace'] as $trace )
                                        <div>
                                            <a href="phpstorm://open?file={{ urlencode($trace['file_full_path']) }}&line={{ $trace['line'] }}" target="_blank">{{ $trace['file'] }}:{{ $trace['line'] }}</a>
                                            <p>{{ $trace['msg'] }}</p>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No stacktrace data.</p>
                                @endif
                            </div>
                            @if( $log['context'] )
                                <div class="log-context col-xl-55 offset-xl-5">
                                    <h6>Context</h6>
                                    <table class="table">
                                    @foreach( $log['context'] as $key => $context )
                                        <tr>
                                            <th>{{ $key }}</th>
                                            <td>
                                                @if( $key == 'url' )
                                                    <a href="{{ $context }}" target="_blank">{{ $context }}</a>

                                                @elseif( $key == 'userId' )
                                                    <a data-offcanvas-href="{{ route('admin.users.read', $context) }}">{{ $context }}</a>

                                                @else

                                                    @include('admin.app.dev_tools.components.log_context_print_array')

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

            @empty

                <div class="box no-padding">
                    <div class="box-body">No Logs.</div>
                </div>

            @endforelse


    </div>

    {{ admin()->paginate($logs) }}

@endsection
