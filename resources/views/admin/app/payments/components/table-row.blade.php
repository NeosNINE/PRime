<tr data-id="{{ $transaction->id }}" data-offcanvas-href="{{ route('admin.payments.read', ['transaction'=>$transaction->id]) }}">
    <td class="id">{{ $transaction->id }}</td>
    <td class="user">
        @if($transaction->user)
            <a data-offcanvas-href="{{ route('admin.users.read', ['user'=>$transaction->user->id]) }}">{{ $transaction->user->login ?? $transaction->user->email }}</a>
        @else
            —
        @endif
    </td>
    <td class="amount">${{ number_format((float)$transaction->amount_usd, 2, '.', '') }}</td>
    <td class="method text-uppercase">{{ $transaction->method }}</td>
    <td class="status">
        @if($transaction->status==='completed')
            <span class="d-label label-success">Completed</span>
        @elseif($transaction->status==='pending')
            <span class="d-label label-warning">Pending</span>
        @else
            <span class="d-label label-danger">Failed</span>
        @endif
    </td>
    <td class="date">{{ $transaction->created_at }}</td>
    <td class="actions text-nowrap">
        <a href="{{ route('admin.payments.read', ['transaction'=>$transaction->id]) }}" class="btn btn-icon-default" title="Детали"><i class="fa fa-eye"></i></a>
        @if($transaction->status!=='completed')
            <button
                class="btn btn-icon-success"
                data-ajax-action
                data-action="{{ route('admin.payments.accept', ['transaction'=>$transaction->id]) }}"
                data-method="POST"
                data-confirm-text="Вы уверены, что хотите принять этот платеж?"
                data-success-text="Платеж успешно принят."
                data-id="{{ $transaction->id }}"
                data-event="payment.accept"
                title="Принять"
            >
                <i class="fa fa-check"></i>
            </button>
        @endif
        <button
            class="btn btn-icon-danger"
            data-ajax-action
            data-action="{{ route('admin.payments.refund', ['transaction'=>$transaction->id]) }}"
            data-method="POST"
            data-confirm-text="Вы уверены, что хотите вернуть этот платеж?"
            data-success-text="Платеж успешно возвращен."
            data-id="{{ $transaction->id }}"
            data-event="payment.refund"
            title="Возврат"
        >
            <i class="fa fa-rotate-left"></i>
        </button>
    </td>
</tr>


