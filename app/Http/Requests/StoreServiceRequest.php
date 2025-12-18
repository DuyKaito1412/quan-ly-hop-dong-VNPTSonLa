<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Service::class);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:services,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'default_price' => 'nullable|numeric|min:0',
            'solution_id' => 'nullable|exists:solutions,id',
            'is_active' => 'boolean',
        ];
    }
}
