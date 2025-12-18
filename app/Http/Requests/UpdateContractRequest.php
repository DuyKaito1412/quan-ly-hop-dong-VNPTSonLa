<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contract'));
    }

    public function rules(): array
    {
        $contractId = $this->route('contract')->id ?? null;
        
        return [
            'contract_no' => ['required', 'string', 'max:100', Rule::unique('contracts', 'contract_no')->ignore($contractId)],
            'customer_id' => 'required|exists:customers,id',
            'sales_person_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:DRAFT,ACTIVE,NEAR_EXPIRY,EXPIRED,RENEWED,TERMINATED',
            'total_amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
