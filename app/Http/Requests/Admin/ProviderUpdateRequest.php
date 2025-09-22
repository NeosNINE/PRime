<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return roles()->checkAccess('providers.edit');
    }

    public function rules(): array
    {
        $drivers = array_keys(config('service-providers.drivers', []));

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'driver' => ['sometimes', 'required', 'string', Rule::in($drivers)],
            'api_url' => ['sometimes', 'required', 'string', 'max:255', 'url'],
            'api_key' => ['sometimes', 'required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'low_balance_threshold' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
