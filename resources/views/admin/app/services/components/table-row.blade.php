@php
    $statusClass = $service->is_active ? 'label-success' : 'label-danger';
@endphp
<tr>
    <td><input type="checkbox" class="service-checkbox" name="ids[]" value="{{ $service->id }}"></td>
    <td class="text-nowrap">#{{ $service->id }}</td>
    <td>
        <div class="fw-semibold">{{ $service->name }}</div>
        @if($service->external_id)
            <div class="text-muted small">ID провайдера: {{ $service->external_id }}</div>
        @endif
    </td>
    <td>{{ $service->category->name ?? '—' }}</td>
    <td>
        @if($service->provider)
            <div>{{ $service->provider->name }}</div>
            <form method="POST" action="{{ route('admin.services.sync_provider', $service->provider) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link btn-xs p-0">Синхронизировать</button>
            </form>
        @else
            <span class="badge bg-secondary">Ручная</span>
        @endif
    </td>
    <td>{{ number_format($service->cost_price, 4, '.', ' ') }} $</td>
    <td>{{ number_format($service->price, 4, '.', ' ') }} $</td>
    <td>{{ $service->min_quantity }} / {{ $service->max_quantity }}</td>
    <td>{{ $service->total_orders ?? $service->orders_count ?? 0 }}</td>
    <td><span class="label {{ $statusClass }}">{{ $service->is_active ? 'Активна' : 'Отключена' }}</span></td>
    <td class="actions text-end">
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.services.read', $service) }}" class="btn btn-outline-primary"><i class="fa fa-eye"></i></a>
            @if(roles()->checkAccess('services.edit'))
                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-secondary"><i class="fa fa-pencil"></i></a>
            @endif
            @if($service->is_manual && roles()->checkAccess('services.delete'))
                <form method="POST" action="{{ route('admin.services.delete', $service) }}" onsubmit="return confirm('Удалить услугу?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="fa fa-trash"></i></button>
                </form>
            @endif
        </div>
    </td>
</tr>
