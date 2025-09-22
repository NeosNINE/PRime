<?php

namespace App\Services\Providers;

use App\Models\System\Provider;
use Illuminate\Support\Facades\Validator;

class ProvidersService
{
    /**
     * Remove extra whitespace from string payload values when present.
     */
    public function cleanupPayload(array $payload): array
    {
        foreach (['name', 'api_url', 'api_key', 'driver'] as $key) {
            if (array_key_exists($key, $payload) && is_string($payload[$key])) {
                $payload[$key] = trim($payload[$key]);
            }
        }

        return $payload;
    }

    /**
     * Validate provider data for create/update operations.
     */
    public function saveValidate(array $data, ?Provider $model = null): array
    {
        $requiredRule = $model === null ? 'required' : 'sometimes|required';

        $rules = [
            'name' => [$requiredRule, 'string', 'max:255'],
            'api_url' => [$requiredRule, 'string', 'max:2048'],
            'api_key' => [$requiredRule, 'string', 'max:255'],
            'driver' => [$requiredRule, 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        return Validator::make($data, $rules)->validate();
    }

    /**
     * Update an existing provider with the supplied payload.
     */
    public function edit(Provider $provider, array $data): Provider
    {
        $payload = $this->cleanupPayload($data);

        $validated = $this->saveValidate($payload, $provider);

        $provider->fill($validated);
        $provider->save();

        return $provider;
    }

    public function activate(Provider $provider): Provider
    {
        return $this->edit($provider, ['is_active' => true]);
    }

    public function deactivate(Provider $provider): Provider
    {
        return $this->edit($provider, ['is_active' => false]);
    }

    public function create(array $data): Provider
    {
        $payload = $this->cleanupPayload($data);
        $validated = $this->saveValidate($payload);

        if (!array_key_exists('is_active', $validated)) {
            $validated['is_active'] = true;
        }

        return Provider::create($validated);
    }
}
