@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'data_always_active' => true
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            <select
                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                data-input-lang="{{ $lang_key }}"
                multiple

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )
            >
                @foreach( $item['select_elements'] as $k => $v )

                    <option value="{{ $k }}" @if( isset($field_value[$lang_key]) && in_array($k, (array)$field_value[$lang_key]) ) selected @endif >{{ $v }}</option>

                @endforeach
            </select>


        @endforeach

    @else

        <select

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

            @if( $item['select2'] )
                class="select2"
            @endif

            multiple

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )
        >
            @foreach( $item['select_elements'] as $k => $v )

                <option value="{{ $k }}" @if( isset($field_value) && in_array($k, (array)$field_value) ) selected @endif >{{ $v }}</option>

            @endforeach
        </select>

    @endif

@endcomponent
