@if( is_array($context) )

    <div class="context_array">
        <table class="table">
            @foreach( $context as $key => $val )

                @if( is_array($val) )
                    <tr>
                        <th>{{ $key }}</th>
                        <td>
                            @include('admin.app.dev_tools.components.log_context_print_array', ['context' => $val])
                        </td>
                    </tr>

                @else
                    <tr>
                        <th>{{ $key }}</th>
                        <td><pre>{{ print_r($val,1) }}</pre></td>
                    </tr>
                @endif

            @endforeach
        </table>
    </div>

@else
    <pre>{{ print_r($context,1) }}</pre>
@endif
