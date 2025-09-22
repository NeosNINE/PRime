<?php

namespace App\Services\Providers;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\Provider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProvidersService extends Service
{
    use ServiceTrait;

    protected function defaultGetParams(): array
    {
        $params = parent::defaultGetParams();
        $params['sort_by'] = 'created_at';
        return $params;
    }

    protected function argsPrepareForGetModelAllQuery(array $args): array
    {
        $args = parent::argsPrepareForGetModelAllQuery($args);

        if (isset($args['status']) && $args['status'] !== '') {
            $args['fields']['is_active'] = $args['status'] === 'active';
        }

        return $args;
    }

    protected function addDataPrepare(array $data): array
    {
        $data['balance'] = $data['balance'] ?? 0;
        $data['currency'] = $data['currency'] ?? 'USD';
        $data['is_active'] = Arr::get($data, 'is_active', true);

        return $this->cleanupPayload($data);
    }

    protected function editDataPrepare(array $data): array
    {
        return $this->cleanupPayload($data);
    }

    protected function cleanupPayload(array $data): array
    {
        foreach (['name', 'api_url', 'api_key', 'driver'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $data[$field] = trim((string) $data[$field]);
            }
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = $data['is_active'] ? 1 : 0;
        }

        if (array_key_exists('low_balance_threshold', $data) && $data['low_balance_threshold'] !== null) {
            $data['low_balance_threshold'] = (float) $data['low_balance_threshold'];
        }

        return $data;
    }

    public function saveValidate(array $data, $model = null): void
    {
        $drivers = array_keys(config('service-providers.drivers'));

        Validator::make($data, [
            'name' => array_merge($model ? ['sometimes', 'required'] : ['required'], ['string', 'max:255']),
            'driver' => array_merge($model ? ['sometimes', 'required'] : ['required'], ['string', Rule::in($drivers)]),
            'api_url' => array_merge($model ? ['sometimes', 'required'] : ['required'], ['string', 'max:255', 'url']),
            'api_key' => array_merge($model ? ['sometimes', 'required'] : ['required'], ['string']),
            'is_active' => ['boolean'],
            'low_balance_threshold' => ['nullable', 'numeric', 'min:0'],
        ])->validate();
    }

    protected function afterSave(array $data, $model): void
    {
        if ($model instanceof Provider) {
            if (!$model->low_balance_threshold || $model->balance >= $model->low_balance_threshold) {
                $model->forceFill(['last_balance_notification_at' => null])->save();
            }
        }
    }
}
