<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Contract::class);
    }

    public function rules(): array
    {
        return [
            'contract_no' => 'required|string|max:100|unique:contracts,contract_no',
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
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.cycle' => 'nullable|in:MONTHLY,QUARTERLY,YEARLY,ONE_TIME',
        ];
    }
}
