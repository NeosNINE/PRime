@php
    use Illuminate\Support\Str;
@endphp
<tr data-id="{{ $provider->id }}" @if( roles()->checkAccess('providers.read') ) data-offcanvas-href="{{ route('admin.providers.read', $provider) }}" @endif>
    <td class="id">#{{ $provider->id }}</td>
    <td>
        <div class="fw-semibold">{{ $provider->name }}</div>
        <div class="text-muted small">{{ $driverLabels[$provider->driver] ?? Str::title($provider->driver) }}</div>
    </td>
    <td>
        <strong>{{ number_format((float) $provider->balance, 2, '.', ' ') }}</strong>
        <span class="text-muted">{{ $provider->currency }}</span>
    </td>
    <td>{{ optional($provider->last_synced_at)->format('d.m.Y H:i') ?? '—' }}</td>
    <td>
        @if($provider->is_active)
            <span class="d-label d-label-success">Активен</span>
        @else
            <span class="d-label d-label-secondary">Отключен</span>
        @endif
    </td>
    <td class="actions">
        @if( roles()->checkAccess('providers.edit') )
            <button
                class="btn btn-icon-default"
                data-ajax-action
                data-action="{{ route('admin.providers.sync_balance', $provider) }}"
                data-method="POST"
                data-success-text="Баланс обновлён"
                data-event="provider.sync"
                title="Обновить баланс"
            >
                <i class="fa fa-arrows-rotate"></i>
            </button>
            <a data-offcanvas-href="{{ route('admin.providers.edit', $provider) }}" class="btn btn-icon-success" title="Редактировать">
                <i class="fa fa-pen-to-square"></i>
            </a>
            @if($provider->is_active)
                <button
                    class="btn btn-icon-default"
                    data-ajax-action
                    data-action="{{ route('admin.providers.deactivate', $provider) }}"
                    data-method="POST"
                    data-confirm-text="Отключить провайдера?"
                    data-success-text="Провайдер отключён"
                    data-event="provider.deactivate"
                    title="Отключить"
                >
                    <i class="fa fa-toggle-on"></i>
                </button>
            @else
                <button
                    class="btn btn-icon-success"
                    data-ajax-action
                    data-action="{{ route('admin.providers.activate', $provider) }}"
                    data-method="POST"
                    data-success-text="Провайдер активирован"
                    data-event="provider.activate"
                    title="Активировать"
                >
                    <i class="fa fa-toggle-off"></i>
                </button>
            @endif
        @endif
    </td>
</tr>
