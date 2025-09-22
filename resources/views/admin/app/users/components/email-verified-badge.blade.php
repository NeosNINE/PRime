@if( config('settings.users_must_verify_email') )
    @if( $user->email_verified_at )
        <span class="email-verified verified" data-bs-toggle="tooltip" data-bs-title="Email подтвержден" data-bs-custom-class="tooltip-success"><i class="fa fa-circle-check"></i></span>
    @else
        <span class="email-verified not-verified" data-bs-toggle="tooltip" data-bs-title="Email не подтвержден"><i class="fa fa-circle-xmark"></i></span>
    @endif
@endif
