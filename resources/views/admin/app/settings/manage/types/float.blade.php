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

            <input
                type="number"
                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"
                value="{{ $field_value[$lang_key] ?? '' }}"

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                @if( !$item['autocomplete'] )
                    {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                    autocomplete="new-password"
                @endif

                data-input-lang="{{ $lang_key }}"

                step="{{ $item['step'] }}"
            >

        @endforeach

    @else

        <input
            type="number"
            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"
            value="{{ $field_value ?? '' }}"

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            @if( !$item['autocomplete'] )
                {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                autocomplete="new-password"
            @endif

            step="{{ $item['step'] }}"
        >

    @endif

@endcomponent
