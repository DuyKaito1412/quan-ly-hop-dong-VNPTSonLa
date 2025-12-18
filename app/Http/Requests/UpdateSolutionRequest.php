<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('solution'));
    }

    public function rules(): array
    {
        $solutionId = $this->route('solution')->id ?? null;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('solutions', 'code')->ignore($solutionId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}


