@if( request()->input('section') == 'admin' && auth()->check() && roles()->checkAccess('*') )

    @include('auth.confirm-password-admin')

@else

    @include('auth.confirm-password-guest')

@endif
