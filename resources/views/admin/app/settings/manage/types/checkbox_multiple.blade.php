@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'data_always_active' => true
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            <div class="radio-block-lang" data-input-lang="{{ $lang_key }}" data-input-lang-flex="true">
            @foreach($item['checkbox_elements'] as $k => $v )
                <label>
                    <input
                        type="checkbox"

                        {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}][]"

                        value="{{ $k }}"

                        @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                        @if( isset($field_value[$lang_key]) && in_array($k, (array)$field_value[$lang_key]) ) checked @endif

                    > {{ $v }}
                </label>
            @endforeach
            </div>

        @endforeach

    @else

        @foreach($item['checkbox_elements'] as $k => $v )

            <label>
                <input
                    type="checkbox"

                {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[]"

                @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                value="{{ $k }}"

                @if( isset($field_value) && in_array($k, (array)$field_value) ) checked @endif

                > {{ $v }}
            </label>

        @endforeach


    @endif

@endcomponent
