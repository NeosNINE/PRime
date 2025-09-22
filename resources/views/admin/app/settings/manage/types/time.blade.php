@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'data_always_active' => true
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            @php
                if( isset($field_value[$lang_key]) && is_array($field_value[$lang_key]) ){
                    $field_value[$lang_key] = json_encode($field_value[$lang_key]);
                }
            @endphp

            <input
                type="time"

                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"
                value="{{ $field_value[$lang_key] ?? '' }}"

                @if( !$item['autocomplete'] )
                    {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                    autocomplete="new-password"
                @endif

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                data-input-lang="{{ $lang_key }}"
            >

        @endforeach

    @else

        <input
            type="time"

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"
            value="{{ $field_value ?? '' }}"

            @if( !$item['autocomplete'] )
                {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                autocomplete="new-password"
            @endif

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

        >

    @endif

@endcomponent
