<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceMarkupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scope' => ['required', 'in:global,provider,category,service'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'percent' => ['nullable', 'numeric'],
            'fixed' => ['nullable', 'numeric'],
        ];
    }
}
