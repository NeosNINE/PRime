@if( $item['localization'] )
    @include('admin.components.language_choose')
@endif
@if( $item['type'] == 'radio' || $item['type'] == 'checkbox_multiple' )
    <div class="input radios {{ $extra_css_class ?? '' }}" data-always-active="true">

            <span class="label">{{ $item['title'] ?? $item['key'] ?? $item['manage_key'] ?? $parent_item['manage_key'] ?? '' }}</span>

            {{ $slot }}

        @if( roles()->isDeveloper() )
            <span class="input-copy-text" data-copy-text>config('settings.{{ $item['manage_key'] ?? $parent_item['manage_key'] ?? '' }}')</span>
        @endif

    </div>
@else
    <div class="input {{ $extra_css_class ?? '' }}" @if( isset($data_always_active) && $data_always_active) data-always-active="true" @endif >
        <label>
            @if( !$item['title_and_desc_on_top'] )
                <span class="label">{{ $item['title'] ?? $item['key'] ?? $item['manage_key'] ?? $parent_item['manage_key'] ?? '' }}</span>
            @endif

            {{ $slot }}

            @if( roles()->isDeveloper() )
                <span class="input-copy-text" data-copy-text>config('settings.{{ $item['manage_key'] ?? $parent_item['manage_key'] ?? '' }}')</span>
            @endif

        </label>
    </div>
@endif

@if( isset($item['desc']) && !$item['title_and_desc_on_top'] )
    <p class="item-input-desc">{{ $item['desc'] }}</p>
@endif
