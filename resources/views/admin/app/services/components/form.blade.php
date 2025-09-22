@php
    $service = $service ?? null;
    $isManual = $service ? $service->is_manual : true;
@endphp
<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Название</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $service->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Провайдер</label>
        <select name="provider_id" class="form-select select2">
            <option value="">Ручная услуга</option>
            @foreach($providers as $provider)
                <option value="{{ $provider->id }}" @selected(old('provider_id', $service->provider_id ?? '') == $provider->id)>{{ $provider->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Категория</label>
        <select name="category_id" class="form-select select2">
            <option value="">Создать новую</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $service->service_category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Новая категория</label>
        <input type="text" name="new_category_name" class="form-control" value="{{ old('new_category_name') }}" placeholder="Введите название категории">
    </div>
    <div class="col-12">
        <label class="form-label">Описание</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $service->description ?? '') }}</textarea>
    </div>
    <div class="col-md-3">
        <label class="form-label">Минимальное количество</label>
        <input type="number" min="1" name="min_quantity" class="form-control" value="{{ old('min_quantity', $service->min_quantity ?? 1) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Максимальное количество</label>
        <input type="number" min="1" name="max_quantity" class="form-control" value="{{ old('max_quantity', $service->max_quantity ?? 1000) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Закупочная цена (за 1000)</label>
        <input type="number" step="0.0001" min="0" name="cost_price" class="form-control" value="{{ old('cost_price', $service->cost_price ?? 0) }}" {{ $service && !$isManual ? 'readonly' : '' }}>
    </div>
    <div class="col-md-3">
        <label class="form-label">Цена для клиента (за 1000)</label>
        <input type="number" step="0.0001" min="0" name="price" class="form-control" value="{{ old('price', $service->price ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Статус</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected(old('is_active', $service->is_active ?? true))>Активна</option>
            <option value="0" @selected(!old('is_active', $service->is_active ?? true))>Отключена</option>
        </select>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</div>
