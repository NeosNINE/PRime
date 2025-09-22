@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'data_always_active' => true
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            <input
                type="checkbox"

                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                data-input-lang="{{ $lang_key }}"

                class="switch"

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                value="true"
                data-value-not-checked="false"

                @if( isset($field_value[$lang_key]) && $field_value[$lang_key] )
                    checked
                @endif

            >

        @endforeach

    @else

        <input
            type="checkbox"

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

            class="switch"

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            value="true"
            data-value-not-checked="false"

            @if( $field_value )
                checked
            @endif
        >

    @endif

@endcomponent
