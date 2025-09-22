@component('admin.app.settings.manage.input_layout', [
    'item' => $item,
    'parent_item' => $parent_item ?? null,
    'extra_css_class' => 'wysiwyg-editor'
])

    @if( $item['localization'] )

        @foreach( config('settings.languages') as $lang_key => $lang )

            @php
                if( isset($field_value[$lang_key]) && is_array($field_value[$lang_key]) ){
                    $field_value[$lang_key] = json_encode($field_value[$lang_key]);
                }
            @endphp

            <div data-input-lang="{{ $lang_key }}">
                <textarea

                    {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}[{{ $lang_key }}]"

                    data-editor

                    @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                >{{ $field_value[$lang_key] ?? '' }}</textarea>
            </div>

        @endforeach

    @else

        <textarea

            {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $full_manage_key_path }}"

            data-editor

            @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

        >{{ $field_value ?? '' }}</textarea>

    @endif

@endcomponent
