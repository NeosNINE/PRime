<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return roles()->checkAccess('providers.add');
    }

    public function rules(): array
    {
        $drivers = array_keys(config('service-providers.drivers', []));

        return [
            'name' => ['required', 'string', 'max:255'],
            'driver' => ['required', 'string', Rule::in($drivers)],
            'api_url' => ['required', 'string', 'max:255', 'url'],
            'api_key' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'low_balance_threshold' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
