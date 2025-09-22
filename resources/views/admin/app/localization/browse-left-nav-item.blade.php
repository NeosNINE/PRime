@if( langText($section->name) != 'Административная панель' )
<li>
    <a href="{{ route('admin.localization.browse', ['section' => $section->id] ) }}">{{ langText($section->name) }}</a>
    @if( count($section->sections) )
        <ul>
            @each('admin.app.localization.browse-left-nav-item', $section->sections, 'section')
        </ul>
    @endif
</li>
@endif
