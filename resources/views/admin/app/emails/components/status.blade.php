@if( $email->status == 'error' )

    <span class="text-with-icon color-error" @if( isset($email->data['error_msg']) ) data-bs-toggle="tooltip" data-bs-title="{{ $email->data['error_msg'] }}" data-bs-custom-class="tooltip-error" @endif ><i class="fa fa-times-circle"></i> Не отправлено</span>

@elseif( $email->status == 'success' )

    <span class="text-with-icon color-success"><i class="fa fa-check-circle"></i> Успешно отправлено</span>

@elseif( $email->status == 'sending' )

    <span class="text-with-icon color-primary"><i class="fa fa-circle-notch fa-spin"></i> Отправка...</span>

@else

    {{ $email->status }}

@endif
