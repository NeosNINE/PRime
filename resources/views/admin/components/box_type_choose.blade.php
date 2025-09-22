<div class="box-type-choose">

    @if( !isset($disabled_all_link) )
        <a href="{{ route($all_route) }}" @if( !request()->input('fields') ) class="active" @endif>Все</a>
    @endif

    @foreach( $items_arr as $item_key => $item_val )

        @php

            $query_arr = [
                'fields' => [
                    $field => $item_key
                ]
            ];

            if( request()->input('search') )
                $query_arr['search'] = request()->input('search');

        @endphp

        <a href="{{ route($all_route, $query_arr) }}" @if( request()->input('fields.'.$field) == $item_key ) class="active" @endif >{{ $item_val }}</a>

    @endforeach
</div>