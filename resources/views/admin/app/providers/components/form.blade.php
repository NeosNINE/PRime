@php
    use Illuminate\Support\Str;
    $isRead = $is_read ?? false;
    $provider = $provider ?? null;
    $driverLabels = $driverLabels ?? config('service-providers.labels', []);
@endphp

<div class="input">
    <label>
        <span class="label">Название <i>*</i></span>
        <input type="text" name="name" value="{{ old('name', $provider->name ?? '') }}" @if($isRead) disabled @endif required>
    </label>
    @error('name')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Драйвер <i>*</i></span>
        <select name="driver" class="form-select select2" @if($isRead) disabled @endif required>
            @foreach($availableDrivers as $driver)
                <option value="{{ $driver }}" @selected(old('driver', $provider->driver ?? '') === $driver)>
                    {{ $driverLabels[$driver] ?? Str::title($driver) }}
                </option>
            @endforeach
        </select>
    </label>
    @error('driver')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">API URL <i>*</i></span>
        <input type="url" name="api_url" value="{{ old('api_url', $provider->api_url ?? '') }}" @if($isRead) disabled @endif required>
    </label>
    @error('api_url')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">API ключ <i>*</i></span>
        <input type="text" name="api_key" value="{{ old('api_key', $provider->api_key ?? '') }}" @if($isRead) disabled @endif required>
    </label>
    @error('api_key')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Статус</span>
        <select name="is_active" class="form-select select2" @if($isRead) disabled @endif>
            <option value="1" @selected((int)old('is_active', (int)($provider->is_active ?? 1)) === 1)>Активен</option>
            <option value="0" @selected((int)old('is_active', (int)($provider->is_active ?? 1)) === 0)>Отключен</option>
        </select>
    </label>
    @error('is_active')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Порог низкого баланса ({{ $provider->currency ?? 'USD' }})</span>
        <input type="number" step="0.01" min="0" name="low_balance_threshold" value="{{ old('low_balance_threshold', $provider->low_balance_threshold ?? '') }}" @if($isRead) disabled @endif>
    </label>
    @error('low_balance_threshold')<div class="error">{{ $message }}</div>@enderror
</div>

@if($provider)
    <div class="input">
        <label>
            <span class="label">Текущий баланс</span>
            <input type="text" value="{{ number_format((float)($provider->balance ?? 0), 2, '.', ' ') }} {{ $provider->currency }}" disabled>
        </label>
    </div>

    <div class="input">
        <label>
            <span class="label">Последняя синхронизация</span>
            <input type="text" value="{{ optional($provider->last_synced_at)->format('d.m.Y H:i') ?? '—' }}" disabled>
        </label>
    </div>

    <div class="input">
        <label>
            <span class="label">Синхронизация услуг</span>
            <input type="text" value="{{ optional($provider->services_last_synced_at)->format('d.m.Y H:i') ?? '—' }}" disabled>
        </label>
    </div>
@endif
