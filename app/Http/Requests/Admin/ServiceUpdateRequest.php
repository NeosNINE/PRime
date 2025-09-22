<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'new_category_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'min_quantity' => ['sometimes', 'integer', 'min:1'],
            'max_quantity' => ['sometimes', 'integer', 'gte:min_quantity'],
            'cost_price' => ['sometimes', 'numeric', 'min:0'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
