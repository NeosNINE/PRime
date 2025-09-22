@if( isset($sort_fields) )

    <?php

        $current_link = route(\Illuminate\Support\Facades\Route::currentRouteName());

    ?>

    @foreach( $sort_fields as $field_key => $field_name )

        <?php

            $query_params = [
                'sort_by' => $field_key ?? null,
                'sort_order' => ( strtolower(request()->input('sort_order')) == 'asc') ?  'desc' : "asc"
            ];

            if( request()->input('search') )
                $query_params['search'] = request()->input('search');

            if( request()->input('fields') )
                $query_params['fields'] = request()->input('fields');

            $query_params = '?'.http_build_query($query_params);

        ?>
        @if( $field_key == request()->input('sort_by') )

            <th><a href="{{ $current_link.$query_params }}" class="table-sort-link">{{ $field_name }} <i class="fas fa-sort-{{ ( strtolower(request()->input('sort_order') ) == 'asc') ? 'down' : 'up' }}"></i></a></th>

        @else

            <th><a href="{{ $current_link.$query_params }}" class="table-sort-link">{{ $field_name }}</a></th>

        @endif

    @endforeach

@endif
