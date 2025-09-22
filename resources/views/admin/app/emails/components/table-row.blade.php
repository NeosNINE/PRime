<tr @if( roles()->checkAccess('emails.read') ) data-offcanvas-href="{{ route('admin.emails.read', $email) }}" data-css-classes="offcanvas-end huge" @endif data-id="{{ $email->id }}" @class(['disabled' => $email->trashed() ])>
    <td>
        @if( $email->user )

            <a href="" data-offcanvas-href="{{ route('admin.users.read', $email->user) }}">{{ $email->email }}</a>

        @else

            {{ $email->email }}

        @endif
    </td>
    <td>{{ $email->subject }}</td>
    <td class="email-status">
        @include('admin.app.emails.components.status')
    </td>
    <td>@if( $email->sent_date ) {{ $email->sent_date->calendar() }} @else - @endif</td>
    <td class="actions">
        @if( roles()->checkAccess('emails.resend') && $email->status == 'error' )
            <button
                class="btn btn-icon-default btn-resend-email"
                data-action="{{ route('admin.emails.resend', $email) }}"
            >
                <i class="fa fa-retweet fa-beat"></i>
            </button>
        @endif
        @if( roles()->checkAccess('emails.delete') )
            <button
                class="btn btn-icon-danger"
                data-delete-object
                data-action="{{ route('admin.emails.delete', $email) }}"
                data-confirm-text="Вы уверены, что желаете удалить Email сообщение?"
                data-success-text="Email сообщение успешно удалено."
                data-id="{{ $email->id }}"
                data-event="email.delete"
            >
                <i class="fa fa-trash-alt"></i>
            </button>
            <button class="btn btn-icon-default" data-restore-object @if( !$email->trashed() ) data-skip-confirm @endif data-action="{{ route('admin.emails.restore', $email) }}" data-confirm-text="Вы уверены, что желаете восстановить Email?" data-success-text="Email &quot;{{ $email->email . r($email->subject, ': '.$email->subject) }}&quot; успешно восстановлено."><i class="fa fa-trash-arrow-up"></i> <span>Восстановить</span></button>
        @endif
    </td>
</tr>
