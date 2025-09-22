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

            <div data-input-lang="{{ $lang_key }}">
                <span
                    data-file-upload="true"
                    data-type="images"
                    data-value="{{ $field_value[$lang_key] ?? '' }}"

                    @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                    @if( isset($default_item) && $default_item )
                        data-name="{{ $full_manage_key_path }}[{{ $lang_key }}]"
                    @else
                        data-input-name="{{ $full_manage_key_path }}[{{ $lang_key }}]"
                    @endif

                ></span>
            </div>


        @endforeach

    @else

        <span
            data-file-upload="true"
            data-type="images"
            data-value="{{ $field_value ?? '' }}"

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            @if( isset($default_item) && $default_item )
                data-name="{{ $full_manage_key_path }}"
            @else
                data-input-name="{{ $full_manage_key_path }}"
            @endif

        ></span>

    @endif

@endcomponent
