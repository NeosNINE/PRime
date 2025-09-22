@foreach( admin()->navRegisterOverTop() as $link )

    @if( !isset($link['access']) || roles()->checkAccess($link['access'], all_accesses_mode: $link['all_accesses_mode']) )

        @isset($link['hr_before'])
            <li class="hr"></li>
        @endisset

        <li>
            <a {!! navigation()->getAttributes($link) !!} class="{{ navigation()->getCSSClasses($link) }}">
                @include('admin.components.navigation.link_text')
            </a>
        </li>

    @endif

@endforeach
