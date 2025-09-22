@if( $errors->isNotEmpty() )

    <div class="error">
        @foreach( $errors->getMessages() as $messages )
            @foreach( $messages as $msg )
                <p>{{ $msg }}</p>
            @endforeach
        @endforeach
    </div>

@endif
