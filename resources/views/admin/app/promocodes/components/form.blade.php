<div class="input">
    <label>
        <span class="label">Код <i>*</i></span>
        <input type="text" name="code" value="{{ old('code', $item->code ?? '') }}" required>
    </label>
    @error('code')<div class="error">{{ $message }}</div>@enderror
    <span class="tooltip-btn" data-bs-title="Уникальный код промокода"></span>
    </div>

<div class="input">
    <label>
        <span class="label">Тип <i>*</i></span>
        <select name="type" class="form-select select2">
            <option value="general" @if(old('type', $item->type ?? 'general') === 'general') selected @endif>Общий</option>
            <option value="individual" @if(old('type', $item->type ?? 'general') === 'individual') selected @endif>Индивидуальный</option>
        </select>
    </label>
    @error('type')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Сумма бонуса (USD) <i>*</i></span>
        <input type="number" step="0.01" name="bonus_amount" value="{{ old('bonus_amount', $item->bonus_amount ?? '') }}" required>
    </label>
    @error('bonus_amount')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Срок действия</span>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', isset($item->expires_at) && $item->expires_at ? $item->expires_at->format('Y-m-d\TH:i') : '') }}">
    </label>
    @error('expires_at')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Статус</span>
        <select name="active" class="form-select select2">
            <option value="1" @if((int)old('active', (int)($item->active ?? 1)) === 1) selected @endif>Активен</option>
            <option value="0" @if((int)old('active', (int)($item->active ?? 1)) === 0) selected @endif>Неактивен</option>
        </select>
    </label>
    @error('active')<div class="error">{{ $message }}</div>@enderror
</div>

<div class="input">
    <label>
        <span class="label">Привязка к username (для индивидуальных)</span>
        <input type="text" name="usernames" value="{{ old('usernames', (isset($item) && ($item->type ?? null) === 'individual') ? $item->users()->pluck('login')->join(', ') : '') }}" placeholder="user1, user2">
    </label>
    @error('usernames')<div class="error">{{ $message }}</div>@enderror
</div>


