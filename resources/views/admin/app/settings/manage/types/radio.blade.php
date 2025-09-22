@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'data_always_active' => true
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            <div class="radio-block-lang" data-input-lang="{{ $lang_key }}" data-input-lang-flex="true">
            @foreach($item['radio_elements'] as $k => $v )
                <label>
                    <input
                        type="radio"

                        {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                        @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                        value="{{ $k }}"

                        @if( isset($field_value[$lang_key]) && $field_value[$lang_key] == $k )
                            checked
                        @endif

                    > {{ $v }}
                </label>
            @endforeach
            </div>

        @endforeach

    @else

        @foreach($item['radio_elements'] as $k => $v )

            <label>
                <input
                    type="radio"

                    {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

                    @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                    value="{{ $k }}"

                    @if( $field_value == $k )
                        checked
                    @endif

                > {{ $v }}
            </label>

        @endforeach


    @endif

@endcomponent
