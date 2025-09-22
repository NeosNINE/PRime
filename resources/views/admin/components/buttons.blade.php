@foreach( $buttons as $button )

    @if( array_key_first($button) == 0 )

        <div class="btn-group" role="group">

            @foreach( $button as $btn)
                @include('admin.components.button', ['button' => $btn])
            @endforeach

        </div>

    @else

        @include('admin.components.button')

    @endif

@endforeach
