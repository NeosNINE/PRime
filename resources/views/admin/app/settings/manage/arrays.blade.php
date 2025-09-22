@if( isset($item['before']) && $item['before'] == 'br' )
    <div class="input-length-100"></div>
@endif
@if( isset($item['before']) && $item['before'] == 'hr' )
    <div class="input-length-100 settings-hr">
        <hr>
    </div>
@endif
@if( isset($item['before_title']) && $item['before_title'] )
    <div class="input-length-100">
        <h5 class="item-title">{{ $item['before_title'] }}</h5>
    </div>
@endif
@if( isset($item['before_desc']) && $item['before_desc'] )
    <div class="input-length-100">
        <h5 class="item-desc">{{ $item['before_desc'] }}</h5>
    </div>
@endif
<div
    class="settings_item_array"
    data-type="{{ $item['type'] }}"

    @if( isset($item['manage_key']) )
        data-manage-key="{{ $item['manage_key'] }}"
    @endif

    data-last-key="{{ $last_key }}"

    data-min-elements="{{ $item['min_elements'] }}"
    data-max-elements="{{ $item['max_elements'] }}"
>

    @if( isset($item['title']) )
        <h5 class="item-title">{{ $item['title'] }}</h5>
    @endif
    @if( isset($item['desc']) )
        <p class="item-desc">{{ $item['desc'] }}</p>
    @endif

    @if( $item['localization'] )

        @include('admin.components.language_choose', ['css_class' => 'style-2'])

        <div class="block-languages">
            @foreach( config('settings.languages') as $lang_key => $lang )

                    <div class="settings_item_array_items count_in_line_{{ $array_level }}_{{ $item['count_items_in_line'] }}" data-input-lang="{{ $lang_key }}" data-input-lang-flex="true">

                        {!! $array_elems_html[$lang_key] ?? '' !!}

                        @if( $item['can_add_new_elem'] && settings()->checkAccess($item['section_key'], 'edit')  )
                            <div
                                class="add_new_elem @if( isset($array_item['localization']) && $array_item['localization'] ) localization @endif"
                                @if( $count_elements[$lang_key] >= $item['max_elements'] ) style="display:none;"  @endif
                            >
                                <i class="fa fa-plus-circle"></i>
                                <div class="default_item">
                                    {!! $default_item_html[$lang_key] ?? '' !!}
                                </div>
                            </div>
                        @endif

                    </div>

            @endforeach
        </div>

    @else
            <div class="settings_item_array_items count_in_line_{{ $array_level }}_{{ $item['count_items_in_line'] }}">

                {!! $array_elems_html !!}

                @if( $item['can_add_new_elem'] && settings()->checkAccess($item['section_key'], 'edit') )
                    <div
                        class="add_new_elem @if( isset($array_item['localization']) && $array_item['localization'] ) localization @endif"
                        @if( $count_elements >= $item['max_elements'] ) style="display:none;"  @endif
                    >
                        <i class="fa fa-plus-circle"></i>
                        <div class="default_item">
                            {!! $default_item_html !!}
                        </div>
                    </div>
                @endif

            </div>

    @endif

</div>
@if( isset($item['after']) && $item['after'] == 'br' )
    <div class="input-length-100"></div>
@endif
@if( isset($item['after']) && $item['after'] == 'hr' )
    <div class="input-length-100 settings-hr">
        <hr>
    </div>
@endif
@if( isset($item['after_title']) && $item['after_title'] )
    <div class="input-length-100">
        <h5 class="item-title">{{ $item['after_title'] }}</h5>
    </div>
@endif
@if( isset($item['after_desc']) && $item['after_desc'] )
    <div class="input-length-100">
        <h5 class="item-desc">{{ $item['after_desc'] }}</h5>
    </div>
@endif
