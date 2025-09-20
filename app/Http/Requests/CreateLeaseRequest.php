<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create lease');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'lease_id' => 'nullable|string|max:100|unique:leases,lease_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'property_id' => 'required|exists:properties,id',
            'house_id' => 'nullable|exists:houses,id',
            'tenant_id' => 'required|exists:users,id',
            'rent' => 'required|numeric|min:0|max:999999999.99',
            'termination_date_notice' => 'nullable|date|after:start_date',
            'rent_cycle' => 'required|integer|min:1|max:12',
            'invoice_generation_day' => 'required|integer|min:1|max:31',
            'next_billing_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,expired,terminated,pending',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Lease start date is required.',
            'start_date.after_or_equal' => 'Lease start date cannot be in the past.',
            'end_date.required' => 'Lease end date is required.',
            'end_date.after' => 'Lease end date must be after start date.',
            'property_id.required' => 'Property selection is required.',
            'property_id.exists' => 'Selected property does not exist.',
            'tenant_id.required' => 'Tenant selection is required.',
            'tenant_id.exists' => 'Selected tenant does not exist.',
            'rent.required' => 'Rent amount is required.',
            'rent.min' => 'Rent amount must be greater than 0.',
            'rent_cycle.required' => 'Rent cycle is required.',
            'rent_cycle.min' => 'Rent cycle must be at least 1 month.',
            'rent_cycle.max' => 'Rent cycle cannot exceed 12 months.',
            'status.required' => 'Lease status is required.',
            'status.in' => 'Invalid lease status.',
        ];
    }
}
