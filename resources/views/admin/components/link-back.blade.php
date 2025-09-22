@if( url()->previous() != url()->current() )
<a href="{{ url()->previous() }}" class="back-link"><i class="fa fa-long-arrow-alt-left"></i> Назад </a>
@endif
