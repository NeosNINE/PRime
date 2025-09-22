{!! (isset($link['icon']) ? '<i class="'.$link['icon'].'"></i> ' : '') . '<span class="navigation-text">' . $link['text'] . '</span>' !!}
@isset($link['count_key'])<span class="navigation-count {{ r($link['count'], 'active') }}" data-navigation-count-key="{{ $link['count_key'] }}">{{ $link['count'] }}</span>@endisset
