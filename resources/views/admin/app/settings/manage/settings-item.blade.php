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
    class="settings-item input-length-{{ $input_length ?? 100 }}"
    data-key="{{ $data_key ?? 1 }}"
>
    @if( $parent_item && $parent_item['can_delete_elem'] && settings()->checkAccess($item['section_key'], 'edit') )
        <span class="delete_elem"><i class="fa fa-window-close"></i></span>
    @endif
    @if( $specifying_key )
        <div class="specifying_key specifying_key_count_{{ isset($parent_item['fields']) ? count($parent_item['fields']) : 0 }}">
            <div class="input">
                <label>
                    <span class="label">Key</span>

                    <input
                        type="text"
                        {{ isset($default_item) && $default_item ? 'data-name' : 'name' }}="{{ $specifying_key_input_name }}"
                        value="{{ $cfg_key ?? '' }}"

                        @disabled( !settings()->checkAccess($item['section_key'], 'edit') )

                        {{-- Здесь специально указываем new-password, чтобы предотвратить автозаполнение --}}
                        autocomplete="new-password"
                    >

                </label>
            </div>
    @endif

            @if( isset($item['title']) && $item['title_and_desc_on_top'] )
                <h5 class="item-title">{{ $item['title'] }}</h5>
            @endif
            @if( isset($item['desc']) && $item['title_and_desc_on_top'] )
                <p class="item-desc">{{ $item['desc'] }}</p>
            @endif

            {!! $slot !!}

    @if( $specifying_key )
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
