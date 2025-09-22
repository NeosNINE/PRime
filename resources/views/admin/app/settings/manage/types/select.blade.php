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

            <select
                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                data-input-lang="{{ $lang_key }}"

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            >
                @foreach( $item['select_elements'] as $k => $v )

                    <option value="{{ $k }}" @if( isset($field_value[$lang_key]) && $field_value[$lang_key] == $k ) selected @endif >{{ $v }}</option>

                @endforeach
            </select>

        @endforeach

    @else

        <select

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

            @if( $item['select2'] )
                class="select2"
            @endif

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

        >
        @foreach( $item['select_elements'] as $k => $v )

            <option value="{{ $k }}" @if( $field_value == $k ) selected @endif >{{ $v }}</option>

        @endforeach
        </select>

    @endif

@endcomponent
