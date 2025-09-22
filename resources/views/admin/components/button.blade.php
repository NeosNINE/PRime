@if( isset($button['dropdown']) )
    <div class="btn-group" role="group">
@endif

        <a @foreach( $button['attributes'] as $attr_key => $attr_val ) {{ $attr_key }}="{{ $attr_val }}" @endforeach>@if( isset($button['icon']) )<i class="{{ $button['icon'] }}"></i> @endif<span>{{ $button['text'] ?? '' }}</span></a>

@if( isset($button['dropdown']) )

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ $button['dropdown_text'] ?? '' }}</button>
            <ul class="dropdown-menu">
                @foreach( $button['dropdown'] as $btn )
                    <li>@include('admin.components.button', ['button' => $btn])</li>
                @endforeach
            </ul>
        </div>

    </div>
@endif
