@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            @php
                if( isset($field_value[$lang_key]) && is_array($field_value[$lang_key]) ){
                    $field_value[$lang_key] = json_encode($field_value[$lang_key]);
                }
            @endphp

            <textarea
                type="text"
                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                @if( !$item['autocomplete'] )
                    {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                    autocomplete="new-password"
                @endif

                data-input-lang="{{ $lang_key }}"
                rows="{{ $item['rows'] ?? '7' }}"

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            >{{ $field_value[$lang_key] ?? '' }}</textarea>

        @endforeach

    @else

        <textarea
            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

            @if( !$item['autocomplete'] )
                {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                autocomplete="new-password"
            @endif

            rows="{{ $item['rows'] ?? '7' }}"

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

        >{{ $field_value ?? '' }}</textarea>

    @endif

@endcomponent
