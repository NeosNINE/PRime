<div class="language-choose @if( isset($css_class) ) {{ $css_class }} @endif">
    @foreach( config('settings.languages') as $lang_key => $lang )

        <span class="btn @if( $loop->index == 0 ) active @endif" data-lang="{{ $lang_key }}" title="{{ $lang['name'] }}">{{ strtoupper($lang_key) }}</span>

    @endforeach
</div>
