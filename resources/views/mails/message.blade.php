@component('vendor.mail.html.message')
    {!! $message !!}
    @if( isset($btn) )
        @component('vendor.mail.html.button', ['url' => $btn['url']])
            {{ $btn['text'] }}
        @endcomponent
    @endif
@endcomponent
