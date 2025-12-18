<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('service'));
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')->id ?? null;
        
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('services', 'code')->ignore($serviceId)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'default_price' => 'nullable|numeric|min:0',
            'solution_id' => 'nullable|exists:solutions,id',
            'is_active' => 'boolean',
        ];
    }
}
