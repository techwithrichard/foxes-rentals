<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create lease') || auth()->user()->can('edit lease');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $leaseId = $this->route('lease') ? $this->route('lease')->id : null;
        
        return [
            'lease_id' => 'required|string|max:255|unique:leases,lease_id,' . $leaseId,
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'property_id' => 'required|exists:properties,id',
            'house_id' => 'nullable|exists:houses,id',
            'tenant_id' => 'required|exists:users,id',
            'rent' => 'required|numeric|min:0|max:999999',
            'rent_cycle' => 'required|integer|min:1|max:12',
            'invoice_generation_day' => 'required|integer|min:1|max:31',
            'termination_date_notice' => 'nullable|integer|min:1|max:365',
            'status' => 'required|string|in:active,inactive,terminated',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lease_id.required' => 'Lease ID is required.',
            'lease_id.unique' => 'This lease ID already exists.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'property_id.required' => 'Property selection is required.',
            'property_id.exists' => 'Selected property does not exist.',
            'house_id.exists' => 'Selected house does not exist.',
            'tenant_id.required' => 'Tenant selection is required.',
            'tenant_id.exists' => 'Selected tenant does not exist.',
            'rent.required' => 'Rent amount is required.',
            'rent.numeric' => 'Rent amount must be a valid number.',
            'rent.min' => 'Rent amount cannot be negative.',
            'rent.max' => 'Rent amount cannot exceed 999,999.',
            'rent_cycle.required' => 'Rent cycle is required.',
            'rent_cycle.integer' => 'Rent cycle must be a whole number.',
            'rent_cycle.min' => 'Rent cycle must be at least 1 month.',
            'rent_cycle.max' => 'Rent cycle cannot exceed 12 months.',
            'invoice_generation_day.required' => 'Invoice generation day is required.',
            'invoice_generation_day.integer' => 'Invoice generation day must be a whole number.',
            'invoice_generation_day.min' => 'Invoice generation day must be between 1-31.',
            'invoice_generation_day.max' => 'Invoice generation day must be between 1-31.',
            'termination_date_notice.integer' => 'Termination notice must be a whole number.',
            'termination_date_notice.min' => 'Termination notice must be at least 1 day.',
            'termination_date_notice.max' => 'Termination notice cannot exceed 365 days.',
            'status.required' => 'Lease status is required.',
            'status.in' => 'Invalid lease status selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'lease_id' => 'lease ID',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'property_id' => 'property',
            'house_id' => 'house/unit',
            'tenant_id' => 'tenant',
            'rent' => 'rent amount',
            'rent_cycle' => 'rent cycle',
            'invoice_generation_day' => 'invoice generation day',
            'termination_date_notice' => 'termination notice period',
            'status' => 'lease status',
        ];
    }
}
