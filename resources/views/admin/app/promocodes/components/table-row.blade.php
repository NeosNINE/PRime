<tr data-id="{{ $item->id }}" @if( roles()->checkAccess('promo_codes.read') ) data-offcanvas-href="{{ route('admin.promocodes.read', $item) }}" @endif >
    <td>{{ $item->code }}</td>
    <td>{{ $item->type === 'general' ? 'общий' : 'индивидуальный' }}</td>
    <td>${{ number_format($item->bonus_amount, 2) }}</td>
    <td>{{ $item->expires_at ? $item->expires_at->format('Y-m-d H:i') : 'бессрочный' }}</td>
    <td>
        @if($item->type === 'individual')
            {{ $item->users()->pluck('login')->join(', ') }}
        @else
            —
        @endif
    </td>
    <td>
        @if($item->active)
            <span class="d-label d-label-success">Активен</span>
        @else
            <span class="d-label d-label-secondary">Неактивен</span>
        @endif
    </td>
    <td class="actions">
        @if( roles()->checkAccess('promo_codes.edit') )
            <a data-offcanvas-href="{{ route('admin.promocodes.edit', $item) }}" class="btn btn-icon-success"><i class="fa fa-pen-to-square"></i></a>
        @endif
        @if(!$item->active && roles()->checkAccess('promo_codes.edit'))
            <button
                class="btn btn-icon-success"
                data-ajax-action
                data-action="{{ route('admin.promocodes.activate', $item) }}"
                data-method="POST"
                data-success-text="Активировано"
                data-event="promocode.edit"
                title="Активировать"
            >
                <i class="fa fa-toggle-off"></i>
            </button>
        @endif
        @if($item->active)
            <button
                class="btn btn-icon-default"
                data-ajax-action
                data-action="{{ route('admin.promocodes.deactivate', $item) }}"
                data-method="POST"
                data-confirm-text="Деактивировать?"
                data-success-text="Деактивировано"
                data-event="promocode.deactivate"
                title="Деактивировать"
            >
                <i class="fa fa-toggle-on"></i>
            </button>
        @endif
        @if( roles()->checkAccess('promo_codes.delete') )
            <button
                class="btn btn-icon-danger"
                data-delete-object
                data-action="{{ route('admin.promocodes.delete', $item) }}"
                data-confirm-text="Удалить промокод?"
                data-success-text="Промокод удалён."
                data-id="{{ $item->id }}"
                data-event="promocode.delete"
            >
                <i class="fa fa-trash-alt"></i>
            </button>
        @endif
    </td>
</tr>


