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

            <div data-input-lang="{{ $lang_key }}">
                <span

                    data-ace="true"

                    @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                    {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                >{{ $field_value[$lang_key] ?? '' }}</span>
            </div>

        @endforeach

    @else

        <span

            data-ace="true"

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

        >{{ $field_value ?? '' }}</span>

    @endif

@endcomponent
