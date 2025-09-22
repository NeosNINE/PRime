@if( navigation()->checkAccess($link) )

    @isset($link['hr_before'])
        <li class="hr"></li>
    @endisset

    <li data-change-text="{{ $link['change_text'] }}" @class(['active' => navigation()->isActive($link), 'only_for_dev' => navigation()->isAvailableOnlyForDev($link)])>

        @isset($link['sub-nav'])

            <a {!! navigation()->getAttributes($link) !!} class="open-sub-nav {{ navigation()->getCSSClasses($link) }}">
                @include('admin.components.navigation.link_text')
                <i class="icon-sub-nav fa fa-chevron-down"></i>
            </a>

            <div @class(['sub-nav', 'open-to-left' => isset($link['open_to_left'])]) >
                <ul>
                    @foreach( $link['sub-nav'] as $sub_link )

                        @include('admin.components.navigation.link', ['link' => $sub_link])

                    @endforeach
                </ul>
            </div>

        @else

            <a {!! navigation()->getAttributes($link) !!} class="{{ navigation()->getCSSClasses($link) }}">
                @include('admin.components.navigation.link_text')
            </a>

        @endisset

    </li>

@endif
