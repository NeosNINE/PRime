<?php

namespace App\Services\Services;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\Provider;
use App\Models\Service as ServiceModel;
use App\Models\ServiceCategory;
use App\Models\ServiceMarkup;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceMarkupsService extends Service
{
    use ServiceTrait;

    protected ?string $model_key = 'ServiceMarkup';

    public function createOrUpdate(array $data): ServiceMarkup
    {
        $data = $this->preparePayload($data);
        $this->validatePayload($data);

        $identifiers = [
            'scope' => $data['scope'],
            'provider_id' => $data['provider_id'] ?? null,
            'service_category_id' => $data['service_category_id'] ?? null,
            'service_id' => $data['service_id'] ?? null,
        ];

        return ServiceMarkup::query()->updateOrCreate($identifiers, [
            'percent' => $data['percent'],
            'fixed' => $data['fixed'],
        ]);
    }

    public function delete(ServiceMarkup $markup): void
    {
        $markup->delete();
    }

    protected function preparePayload(array $data): array
    {
        $scope = Arr::get($data, 'scope');
        $prepared = [
            'scope' => $scope,
            'provider_id' => Arr::get($data, 'provider_id'),
            'service_category_id' => Arr::get($data, 'service_category_id'),
            'service_id' => Arr::get($data, 'service_id'),
            'percent' => Arr::get($data, 'percent'),
            'fixed' => Arr::get($data, 'fixed'),
        ];

        if ($scope !== 'provider') {
            $prepared['provider_id'] = $scope === 'global' ? null : $prepared['provider_id'];
        }

        if ($scope !== 'category') {
            $prepared['service_category_id'] = $scope === 'service' ? Arr::get($data, 'service_category_id') : null;
        }

        if ($scope !== 'service') {
            $prepared['service_id'] = null;
        }

        return $prepared;
    }

    protected function validatePayload(array $data): void
    {
        $scopes = ['global', 'provider', 'category', 'service'];

        Validator::make($data, [
            'scope' => ['required', Rule::in($scopes)],
            'provider_id' => ['nullable', 'exists:' . (new Provider())->getTable() . ',id'],
            'service_category_id' => ['nullable', 'exists:' . (new ServiceCategory())->getTable() . ',id'],
            'service_id' => ['nullable', 'exists:' . (new ServiceModel())->getTable() . ',id'],
            'percent' => ['nullable', 'numeric'],
            'fixed' => ['nullable', 'numeric'],
        ])->after(function ($validator) use ($data) {
            $percent = $data['percent'];
            $fixed = $data['fixed'];

            if ($percent === null && $fixed === null) {
                $validator->errors()->add('percent', 'Необходимо указать наценку в процентах или фиксированную сумму.');
            }

            switch ($data['scope']) {
                case 'provider':
                    if (!$data['provider_id']) {
                        $validator->errors()->add('provider_id', 'Укажите провайдера.');
                    }
                    break;
                case 'category':
                    if (!$data['service_category_id']) {
                        $validator->errors()->add('service_category_id', 'Укажите категорию услуг.');
                    }
                    break;
                case 'service':
                    if (!$data['service_id']) {
                        $validator->errors()->add('service_id', 'Укажите услугу.');
                    }
                    break;
            }
        })->validate();
    }
}
